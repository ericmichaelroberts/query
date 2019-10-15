<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once( __DIR__.'/DBObject.php' );
require_once( __DIR__.'/Field.php' );
require_once( __DIR__.'/Group.php' );

require_once( __DIR__.'/QueryTraits.php' );
require_once( __DIR__.'/RenderTraits.php' );

/**
 * Encapsulates complex query-formulation according to standardized business logic supplied by inheriting classes (or defined on-the-fly).
 * @author Eric M. Roberts
 * @version 1.0
 */
class Query extends DBObject {

	use QueryTraits;
	use RenderTraits;

	//public $builds = 0;

	private $sKey = null;
	private $pKey = null;
	private $fKey = null;
	private $fName = null;

	protected $_methods;

	public $joins;
	public $fields;
	public $filters = [];
	public $current_group;
	public $logical_groups;

	public $alias = null;
	public $offset = 0;
	public $limit = null;
	public $index = null;
	public $order = [];
	public $rollup = false;

	public $distinct = false;
	public $select_all = false;
	public $calc_found_rows = false;

	public $where;
	public $having = [];

	public $hierarchy;

	public $blueprint = [
		[ 'build_selection' ],
		[ 'build_from' ],
		[ 'build_index' ],
		[ 'build_joins' ],
		[ 'build_filters' ],
		[ 'build_group' ],
		[ 'build_having' ],
		[ 'build_order' ],
		[ 'build_limit' ]
	];

	public function economize_subsource(){
		// foreach child fields
		// // if not implicated, deselect
		// if subsource has subsource, economize recursively down.


		$this->economize_subselects();

		$this->economize_subjoins();

		// $fields = $this->from->fields;
		// $joins = $this->from->joins;
		//
		// exit( print_r( compact('fields','joins'), 1 ) );
	}

	public function resolve_implication( $fieldName ){
		$field = $this->from->fields->$fieldName;
		if( $field->selected
			||
			$field->grouped
			||
			sizeof( $field->filters )
		){
			return true;
		}else{
			$implicatedDeps = array_keys( array_filter( $field->dep_fields ) );
			while( !empty( $implicatedDeps ) ){
				$secondaryDep = array_shift( $implicatedDeps );
				$implication = $this->resolve_implication( $secondaryDep );
				if( $secondaryDep ){
					return true;
				}
			}
		}
		return false;
	}

	public function economize_subjoins(){
		$joins = array_keys( (array)$this->from->joins );
		foreach( $joins as $joinName ){
			$join = $this->from->joins->$joinName;
			if( $join->joined ){
				$keep = false;
				$constituents = $join->constituents;

				while( !empty( $constituents ) && $keep===false ){
					$fieldName = array_shift( $constituents );
					$field = $this->from->fields->$fieldName;

					$keep = $this->resolve_implication( $fieldName );
				}

				if( $keep===false ){
					$join->joined = 0;
				}
			}
		}
	}

	public function economize_subselects(){
		$subfields = array_keys( (array)$this->from->fields );
		foreach( $subfields as $subfieldName ){
			$subfield = $this->from->fields->$subfieldName;
			if(
				$subfield->selected
				&& $subfield->deselectable
				&& !$subfield->grouped
				&& empty( $subfield->get_dependent_fields() )
			){
				$superior = $subfield->get_superior();
				if( !$this->currently_required( $superior->name ) ){
					$subfield->deselect();
				}
			}
		}
	}

	public function currently_required( $fieldName ){
		$fieldObj = $this->fields->$fieldName;
		return $fieldObj->selected
				||
				$fieldObj->grouped
				||
				!empty( $fieldObj->filters )
				||
				sizeof( $fieldObj->get_dependent_fields() );
	}

	public function alias( $field, $alias ){
		$fields = array_keys( (array)$this->fields );
		if( in_array( $field, $fields, true ) ){
			$this->fields->$field->set_alias( $alias );
		}
	}

	public function profile(){

		$pKey = $this->pKey;
		$fKey = $this->fKey;
		$fName = $this->fName;
		$joins = $this->joins;
		$alias = $this->alias;

		$class = get_class( $this );
		$key = self::StoreObject( $this );

		$fields = new stdClass;
		$fieldKeys = array_keys((array)$this->fields);

		foreach( $fieldKeys as $fld ){
			$fields->$fld = self::StoreObject( $this->fields->$fld );
		}

		return (object)[
			'object'	=>	compact('key','class','pKey','alias'),
			'query'		=>	compact('fKey','fName','joins'),
			'fields'	=>	$fields
		];
	}

	public function assemble_logic(){
		///exit( print_r( $this->filters, 1 ) );
		//if( $this->multilogic && $this->multilevel ){

		$strata = $this->build_strata();
		$hierarchy = $this->build_hierarchies();

		$this->logical_groups = new Group( $this );
		$this->current_group = $this->logical_groups;

		$filteroids = [];

		foreach( $this->filters as $idx => $data ){
			list( $method, $args ) = $data;
			$this->current_group = call_user_func_array(
				[ $this->current_group, $method ],
				$args
			);

			if( sizeof( $args )===2 && is_string( $args[0] ) && is_string( $args[1] ) ){
				list( $fld, $fltr ) = $args;
				!array_key_exists( $fld, $filteroids )
					?	$filteroids[ $fld ][] = $fltr
					:	$filteroids[ $fld ] = [ $fltr ];
			}
		}

		$this->logical_groups->resolve();

		$this->logical_groups->distribute(
			$strata,
			$hierarchy,
			$filteroids
		);
	}

	public function filter(){
		$this->filters[] = [ 'filter', func_get_args() ];
		return $this;
	}

	public function or( $x=null, $y=null ){
		$this->filters[] = [ 'or', func_get_args() ];
		return $this;
	}

	public function and( $x=null, $y=null ){
		$this->filters[] = [ 'and', func_get_args() ];
		return $this;
	}

	public function activate(){
		foreach( $this->fields as $field => $fieldObj ){
			$fieldObj->include( 'select' );
		}
	}

	public function select(){
		$args = func_get_args();
		$exclusive = is_bool( $args[ sizeof( $args ) - 1 ] )
			?	array_pop( $args )
			:	false;
		$selection = sizeof( $args )==0
			?	( '*' )
			:	( sizeof( $args ) > 1
				?	( $args )
				:	( is_array($args[0])
					?	$args[0]
					:	( $args[0]!='*'
						?	[$args[0]]
						:	'*'
					)
				)
		);

		$fields = array_keys( (array)$this->fields );

		if( $selection=='*' ){
			$m = "select_";
			if( !method_exists( $this, $m ) || $this->$m() ){
				$this->select_all = true;
				foreach( $fields as $fieldName ){
				//foreach( $this->fields as $fieldName => $fieldObject ){
					$this->fields->$fieldName->select();
				}
			}
		}else{
			$this->select_all = false;
			foreach( $fields as $field ){
				if( in_array( $field, $selection ) ){
					$m = "select_{$field}";
					if( !method_exists( $this, $m ) || $this->$m() ){
						$this->fields->$field->select();
					}
				}elseif( $exclusive ){
					$this->fields->$field->deselect();
				}
			}
		}
		return $this;
	}

	public function select_only(){
		$this->deselect( '*' );
		return call_user_func_array( [ $this, 'select' ], func_get_args() );
	}

	public function deselect(){
		$fields = func_get_args();
		$fieldNames = array_keys( (array)$this->fields );

		//exit( print_r( compact('fields','fieldNames'), 1 ) );

		if( empty( $fields ) || $fields[0]=='*' ){
			foreach( $fieldNames as $fieldName ){
				$fieldObject = $this->fields->$fieldName;
				if( $fieldObject->deselectable ){
					$fieldObject->deselect();
				}
			}
		}else{
			foreach( $fields as $fieldName ){
			//foreach( $fields as $fieldIdx => $field ){
				//$field = $this->fields->$fieldName;
				if( property_exists( $this->fields, $fieldName ) ){
					$m = "deselect_{$fieldName}";
					//exit( print_r( compact('m','fields'), 1 ) );
					if( !method_exists( $this, $m ) || $this->$m() ){
						$this->fields->$fieldName->deselect();
					}
				}
			}
		}
		return $this;
	}

	public function with_count( $tOrf=true ){
		$this->calc_found_rows = !!$tOrf;
		return $this;
	}

	public function distinct( $val=true ){
		if( !method_exists( $this, 'set_distinct' ) || $this->set_distinct( $val ) ){
			$this->distinct = !!$val;
		}
		return $this;
	}

	public function group(){
		$args = func_get_args();
		$fieldNames = array_keys( (array)$this->fields );
		foreach( $fieldNames as $fieldIdx => $fieldName ){
		//foreach( $this->fields as $field => $fieldObj ){
			$fieldObj = $this->fields->$fieldName;
			if( $fieldObj->groupable && in_array( $fieldName, $args ) ){
				$m = "group_{$fieldName}";
				if( !method_exists( $this, $m ) || $this->$m() ){
					$fieldObj->group();
				}
			}
		}
		return $this;
	}

	public function group_only(){
		$this->ungroup();
		return call_user_func_array( [ $this, 'group' ], func_get_args() );
	}

	public function ungroup(){
		$args = func_get_args();
		$fieldNames = array_keys( (array)$this->fields );
		$ungroupFields = empty( $args ) ? $fieldNames : $args;
		foreach( $fieldNames as $fieldIdx => $fieldName ){
		//foreach( $this->fields as $field => $fieldObj ){
			$fieldObj = $this->fields->$fieldName;
			if( $fieldObj->grouped && in_array( $fieldName, $ungroupFields ) ){
				$m = "ungroup_{$fieldName}";
				if( !method_exists( $this, $m ) || $this->$m() ){
					$fieldObj->ungroup();
				}
			}
		}
		return $this;
	}

	public function limit( $x, $y=null ){
		if( !method_exists( $this, 'set_limit' ) || $this->set_limit( $x, $y ) ){
			if( is_null( $y ) ){
				$this->limit = (int)$x;
			}else{
				$this->offset = (int)$x;
				$this->limit = (int)$y;
			}
		}
		return $this;
	}

	public function unlimit(){
		if( !method_exists( $this, 'set_unlimit' ) || $this->set_unlimit() ){
			$this->limit = null;
			$this->offset( 0 );
		}
		return $this;
	}

	public function order( $x, $y=null ){
		$this->order = [];
		if( is_string( $x ) && is_string( $y ) ){
			$this->order[ $x ] = $y;
		}else{
			foreach( $x as $field => $dir ){
				$this->order[ $field ] = $dir;
			}
		}
		return $this;
	}

	public function rollup( $val=true ){
		if( !method_exists( $this, 'set_rollup' ) || $this->set_rollup( $val ) ){
			$this->rollup = !!$val;
		}
		return $this;
	}

	public function rollup_only(){
		$this->rollup( true );
		return call_user_func_array( [ $this, 'group_only' ], func_get_args() );
	}

	public function force_index( $index ){
		if( !method_exists( $this, 'set_force_index' ) || $this->set_force_index( $index )){
			$this->index = "FORCE INDEX ( {$index} )";
		}
		return $this;
	}

	public function use_index( ){
		$args = func_get_args();
		if( !method_exists( $this, 'set_use_index' ) || call_user_func_array([ $this, 'set_use_index' ], $args )){
			$indices = implode(', ',func_get_args());
			$this->index = "USE INDEX( {$indices} )";
		}
		return $this;
	}

	public function initialize( $schema ){
		if( is_null( $this->fields ) ){
			$this->initialize_schema( $schema );

			$fieldKeys = array_keys((array)$this->fields);

			foreach( $fieldKeys as $fieldIdx => $fieldKey ){
				$this->fields->$fieldKey->activate(); // resolve bindings row-level
			}

			$this->activate();
		}
	}

	public function initialize_outermost( $schema ){
		if( is_null( $this->fields ) ){
			$this->initialize_schema( $schema );

			if( is_null( $this->pKey ) ){
				$this->initialize_field_hierarchies();
			}

			$fieldKeys = array_keys((array)$this->fields);

			foreach( $fieldKeys as $fieldIdx => $fieldKey ){
				$this->fields->$fieldKey->activate(); // resolve bindings row-level
			}

			$this->activate();
		}
	}

	public function initialize_field_hierarchies(){
		$this->hierarchy = new stdClass;
		$fieldKeys = array_keys( $this->fields );

		foreach( $fieldKeys as $fieldIdx => $fieldKey ){
			$field = $this->fields->$fieldKey;
		}
	}

	public function __construct( $parentQuery=null ){
		$this->joins = new stdClass;
		$this->_methods = new stdClass;

		$this->parent_query = $parentQuery;

		$class = get_class( $this );
		if( isset( $class::$Schema ) ){
			$this->initialize_schema( $class::$Schema ); // define bindings query-level

			$fieldKeys = array_keys((array)$this->fields);

			foreach( $fieldKeys as $fieldIdx => $fieldKey ){
				$this->fields->$fieldKey->activate(); // resolve bindings row-level
			}

			$this->activate();	// resolve bindings query-level and down
		}
	}

	public function __call( $method, $args=[] ){
		$parentQuery = $this->parent_query;

		return method_exists( $this, $method )
			?	call_user_func_array( [ $this, $method ], $args )
			:	( property_exists( $this->_methods, $method )
				?	call_user_func_array( $this->_methods->$method, $args )
				:	( !is_null( $this->pKey )
					?	call_user_func_array( [ $this->parent_query, $method ], $args )
					:	$this->throw_exception( __METHOD__, "no method named '{$method}' in scope" )
				)
		);
	}

	public function __toString(){ return $this->clean_string(); }
	//
	// }

	public function clean_string(){
		$output = $this->build();
		return str_replace( ["\n","\t"," "],' ', $output );
	}

	public function is_subquery(){
		return !is_null( $this->pKey );
	}

	public function release_join_requirement( $join, $forWhom ){
		$joinObj = $this->joins->$join;
		return $joinObj->joined = $this->is_implicitly_joined( $join );
	}

	protected function is_implicitly_joined( $join ){
		$joinObj = $this->joins->$join;
		$conFields = $joinObj->constituents;
		foreach( $conFields as $fld ){
			if(
				$this->fields->$fld->selected
				||
				$this->fields->$fld->filtered
				||
				$this->fields->$fld->grouped
			){
				return true;
			}
		}

		return false;
	}

	public function ensure_join_availability( $join, $forWhom ){

		if( !property_exists( $this->joins, $join ) ){
			$these = $this;
			exit(print_r(compact('join','forWhom','these'),1));
		}
		$joinObj = $this->joins->$join;

		foreach( $joinObj->dep_fields as $fieldIdx => $fieldName ){
			$this->fields->$fieldName->ensure_field_availability( $forWhom );
		}
		return $joinObj->joined = true;
	}

	public function get_filter_field_ref( $field ){
		$fieldObject = $this->fields->$field;
		return substr( $fieldObject->refs, 0, 2 )=='->'
			?	$this->call_arrow_function( $fieldObject->refs )
			:	$fieldObject->refs;
	}

	public function get_multilevel(){
		$parentQuery = $this->get_parent_query();
		$childQuery = $this->get_from();

		return !is_null( $parentQuery ) || is_object( $childQuery );
	}

	public function get_multilogic(){
		$outermost = $this->outermost;
		$filters = $outermost->filters;
		foreach( $filters as $filterIdx => $filterSpec ){
			if( $filterSpec[0] === 'or' ){
				return true;
			}
		}
		return false;
	}

	public function set_schema( $schema ){ return $this->sKey = self::StoreObject( (object)$schema ); }
	public function get_schema(){
		$class = get_class( $this );
		return isset( $class::$Schema )
			?	$class::$Schema
			:	self::RetrieveObject( $this->sKey );
	}

	public function get_where_clause(){ return $this->where; }
	public function set_where_clause( $clause ){ return $this->where = $clause; }
	public function isset_where_clause(){ return !is_null( $this->where ); }

	public function get_parent_query(){ return is_null( $this->pKey ) ? null : self::RetrieveObject( $this->pKey ); }
	public function set_parent_query( $qObj ){ return $this->pKey = is_null( $qObj ) ? null : self::StoreObject( $qObj ); }
	public function isset_parent_query(){ return !is_null( $this->pKey ); }

	public function get_from(){ return $this->fName ? $this->fName : self::RetrieveObject( $this->fKey ); }
	public function set_from( $from ){ return is_object( $from ) ? $this->fKey = self::StoreObject( $from ) : $this->fName = $from; }
	public function isset_from(){ return !is_null( $this->fKey ) && !is_null( $this->fName ); }

	public function get_field_hierarchies( $fields=null ){
		$output = new stdClass;
		$hierarchies = new stdClass;
		$minDepth = 0;
		$maxDepth = 0;

		$fieldKeys = array_keys((array)$this->fields );
		foreach( $fieldKeys as $fieldKey ){
		//foreach( $this->fields as $field => $fieldObj ){
			if( is_null( $fields ) || in_array( $fieldKey, $fields ) ){
				//$output->$field = $fieldObj->get_hierarchy();
				$fieldObj = $this->fields->$fieldKey;
				$hierarchy = $fieldObj->get_hierarchy();
				$depth = $this->find_filter_depth( $hierarchy );
				$hierarchies->$field = compact( 'hierarchy', 'depth' );
				$minDepth = $depth < $minDepth ? $depth : $minDepth;
				$maxDepth = $depth > $maxDepth ? $depth : $maxDepth;
			}
		}

		$output->hierarchies = $hierarchies;
		$output->minDepth = $minDepth;
		$output->maxDepth = $maxDepth;

		return $output;
	}

	public function get_outermost(){ return is_null( $this->pKey ) ? $this : $this->parent_query->get_outermost(); }

	protected function apply_initialization_params( $spec ){

		foreach( ['select','deselect','group','ungroup','rollup','force_index','use_index','order'] as $method ){
			if( array_key_exists( $method, $spec ) ){
				call_user_func_array( [ $this, $method ], $spec[$method] );
			}
		}

		if( array_key_exists( 'filter', $spec ) ){
			foreach( $spec['filter'] as $field => $subspec ){
				$filters = is_array( $subspec )
					?	$subspec
					:	[ $subspec ];

				foreach( $subspec as $filterIdx => $filterCnd ){
					$this->fields->$field->filter( $filterCnd, true );
				}

			}
		}
	}

	protected function build_field( $name, $spec=null ){

		list( $fieldName, $fieldSpec ) = is_null( $spec )
			?	[ $name, [] ]
			:	(is_array( $spec )
				?	[ $name, $spec ]
				:	( is_string( $spec ) && property_exists( $this->joins, $spec )
					?	[ $name, [ 'from'=>$spec ]]
					:	[ $name, [ 'selected'=>!!$spec ]]
				)
			);

		return $this->complete_field_spec( $fieldSpec, $name );

	}

	protected function complete_field_spec( $spec, $name ){

		if( strpos( $name, ':' )!==false ){
			$chunks = explode( ':', trim( $name ), 2 );
			$name = trim( array_shift( $chunks ) );
			$spec['name'] = $name;
			if( sizeof( $chunks ) ){
				$x = trim( array_pop( $chunks ) );
				$spec['subordinate'] = $x;
				$spec['synonyms'][] = $x;
			}
		}

		if( substr( trim( $name ), -1 )==')' ){
			$a = substr( trim( $name ), 0, -1 );
			$b = explode( '(', $a, 2 );
			$name = trim( array_shift( $b ) );
			if( sizeof( $b ) ){
				$c = trim( array_pop( $b ) );
				$spec['synonyms'] = preg_split( '/[^a-z|0-9|_]+/i', $c );
			}
		}

		$spec['name'] = $name;

		if( !array_key_exists( 'selectable', $spec ) ){ $spec['selectable'] = true; }

		$from = array_key_exists( 'from', $spec )
			?	$spec['from']
			:	( is_object( $this->from )
				?	true
				:	$this->from);

		$having = false;

		$prefix = $from===true || $from==$this->from
			?	"{$this->alias}."
			:	(property_exists( $this->joins, $from )
				?	$this->extrapolate_join_alias( $this->joins->$from->clause ).'.'
				:	($this->alias ? "{$this->alias}." : ''));

		$filterable = true;
		$filters = [];

		if( !array_key_exists( 'refs', $spec ) ){
			$refs = "{$prefix}{$spec['name']}";
			$spec['refs'] = $refs;
		}

		$spec['from'] = $from;

		if( !array_key_exists( 'filterable', $spec ) ){ $spec['filterable'] = true; }

		$selected = array_key_exists( 'selectable', $spec ) ? !!$spec['selectable'] : true;

		$deselectable = true;
		$grouped = false;
		$groupable = true;

		$output = array_merge(
			compact('from','refs','having','filterable','filters','selected','deselectable','grouped','groupable'),
			$spec
		);

		return $output;
	}

	protected function extrapolate_join_alias( $x ){
		if( preg_match( "/(?:\s(?<alias>[a-z_][a-z_0-9]*)\s+(?:ON|USING)\s*\([^\)]+\))$/i", $x, $m )){
			extract( $m );
		}
		return isset( $alias ) ? $alias : null;
	}


	protected function call_filter_function( $refs, $filter ){
		$filter = substr( $filter, 2 );
		$value = $this->$filter();
		return "{$refs} {$value}";
	}

	protected function call_arrow_function( $str ){
		$fn = substr( $str, 2 );
		return $this->$fn();
	}

	protected function call_refs_function( $refs ){
		$fn = substr( $refs, 2 );
		return $this->$fn();
	}

	protected function call_from_function( $from ){
		$fn = substr( $from, 2 );
		return $this->$fn();
	}
}
