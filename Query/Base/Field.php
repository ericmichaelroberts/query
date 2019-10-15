<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Encapsulated MYSQL Query Field
 * @author Eric M. Roberts
 * @version 1.0
 */

class QueryField extends DBObject {

	protected $refs;
	protected $name;
	protected $from;
	protected $alias;
	protected $primary;
	protected $synonyms = [];

	protected $selected = 0;
	protected $selectable = true;
	protected $deselectable = true;

	protected $grouped = false;
	protected $groupable = true;
	protected $grouping_idx = 0;

	protected $filters = [];
	protected $filterable = true;
	protected $filterTarget = true;
	protected $filterFactor = false;

	protected $select_from_subordinate = false;
	protected $filter_to_subordinate = false;
	protected $group_at_subordinate = false;

	protected $base;
	protected $having = false;

	protected $reqs = [];
	protected $dep_fields = [];

	private $fKey;
	private $qKey;
	private $subKey;
	private $superKey;
	private $schemaKey;

	public function get_dependent_fields(){
		return $this->dep_fields;
	}

	public function set_alias( $alias ){
		return $this->alias = $alias;
	}

	public function &get_hierarchy(){
		if( is_null( $this->hierarchy ) ){
			$this->init_hierarchy();
		}

		return $this->hierarchy;
	}

	public function init_hierarchy(){
		$pointer = $this;
		$this->hierarchy = [];

		do{
			$this->hierarchy[ $pointer->get_key() ] = $pointer->get_level_data();
			$sub = $pointer->subordinate;
			$pointer = $sub;

			$sub->hierarchy =& $this->hierarchy;

		}while( isset( $pointer->subordinate ) );
	}

	public function get_level_data(){
		$output = new stdClass;
		$output->name = $this->name;
		$output->refs = $this->refs;
		$output->from = $this->from;
		$output->alias = $this->alias;
		$output->synonyms =& $this->synonyms;

		return $output;
	}

	public function select(){ $this->include( 'select' ); $this->selected = true; }

	public function deselect(){ $this->exclude( 'select' ); $this->selected = false; }

	public function group(){
		if( $this->grouped ){
			return true;
		}elseif( $this->groupable ){
			$this->grouped = true;
			$this->select();
			return $this->include( 'group' );
		}else{
			return false;
		}
	}

	public function ungroup(){
		if( $this->grouped ){
			$this->grouped = false;
			return $this->exclude( 'group' );
		}else{
			return true;
		}
	}

	public function activate(){

		$fieldSchema = $this->schema;

		if( array_key_exists( 'selected', $fieldSchema ) ){
			$this->init_selected( $fieldSchema );
		}

		if( array_key_exists( 'grouped', $fieldSchema ) ){
			$this->init_grouped( $fieldSchema );
		}

		if( array_key_exists( 'filters', $fieldSchema ) ){
			$this->init_filters( $fieldSchema );
		}

		if( !$this->primary  && $this->from!==true ){
			$from = $this->from;
			$this->query->joins->$from->constituents[] = $this->name;
		}

		foreach( $this->reqs as $reqIdx => $req ){
			if( !property_exists( $this->query->fields, $req ) ){
				exit( $this->name );
			}
			$this->query->fields->$req->dep_fields[ $this->name ] = $this->selected || $this->filtered || $this->grouped;
		}
	}

	public function include( $forWhat=null ){
		if( !$this->primary ){
			$this->query->ensure_join_availability( $this->from, $this->name );
		}else{
			$this->_resolve_reqs();
		}
	}

	public function exclude( $forWhat ){
		if( ( $forWhat=='select' && (!( $this->filtered || $this->grouped )) ) ||
			( $forWhat=='filter' && (!( $this->selected || $this->grouped )) ) ||
			( $forWhat=='group' && (!( $this->filtered || $this->selected )) ) ){
				if( !$this->primary ){
					$this->query->release_join_requirement( $this->from, $this->name );
				}
				$this->_release_reqs( $forWhat );
		}
	}

	public function filter( $filter=null, $direct=false ){
		$subordinate = $this->get_subordinate();
		return $direct
			?	$this->_filter( $filter )
			:	($this->filter_to_subordinate
				?	$this->subordinate->filter( $filter )
				:	($this->filterable
					?	$this->_filter( $filter )
					:	false
				)
			);
	}

	public function get_filtered(){
		return !empty( $this->filters );
	}

	public function release_field_requirement( $forWhom ){
		$this->dep_fields[$forWhom] = false;
		if( !empty( $this->reqs ) ){
			foreach( $this->reqs as $req_field ){
				$this->query->fields->$req_field->release_field_requirement( $this->name );
			}
		}
	}

	public function ensure_field_availability( $forWhom ){
		$this->dep_fields[$forWhom] = true;

		if( !$this->primary ){
			$this->query->ensure_join_availability( $this->from, $this->name );
		}

		if( !empty( $this->reqs ) ){
			foreach( $this->reqs as $req_field ){
				$this->query->fields->$req_field->ensure_field_availability( $this->name );
			}
		}



	}

	public function __construct( $queryObject, $fieldName, $fieldSchema ){
		$this->name = $fieldName;
		$this->query = $queryObject;
		$this->schema = $fieldSchema;

		$this->fKey = self::StoreObject( $this );

		$props = [];
		$checks = explode( ',', 'alias,synonyms,from,refs,reqs,selectable,deselectable,filterable,groupable,grouping_idx' );

		foreach( $checks as $prop ){
			$m = "init_{$prop}";
			if( array_key_exists( $prop, $fieldSchema ) ){
				$props[] = $prop;
				if( method_exists( $this, $m ) ){
					$this->$m( $fieldSchema );
				}else{
					$this->$prop = $fieldSchema[$prop];
				}
			}
		}

		if( is_object( $this->query->from ) ){
			$subfield = array_key_exists( 'subfilter', $fieldSchema ) && $fieldSchema['subfilter']
				?	( $fieldSchema['subfilter']===true
					?	$fieldName
					:	$fieldSchema['subfilter'])
				:	(property_exists( $this->query->from->fields, $fieldName )
					?	$fieldName
					:	false);

			if( $subfield ){
				$this->subordinate = $this->query->from->fields->$subfield;

				$this->subordinate->set_superior( $this );

				$this->select_from_subordinate = $this->selectable && $this->subordinate->selectable;
				$this->group_at_subordinate = $this->groupable && $this->subordinate->groupable;
				$this->filter_to_subordinate = $this->filterable && $this->subordinate->filterable;
			}
		}
	}

	public function get_superior(){ return !is_null( $this->superKey ) ? self::RetrieveObject( $this->superKey ) : null; }
	public function set_superior( $fObj ){ return $this->superKey = self::StoreObject( $fObj ); }
	public function isset_superior(){ return !is_null( $this->superKey ); }

	public function get_subordinate(){ return !is_null( $this->subKey ) ? self::RetrieveObject( $this->subKey ) : null; }
	public function set_subordinate( $fObj ){ return $this->subKey = self::StoreObject( $fObj ); }
	public function isset_subordinate(){ return !is_null( $this->subKey ); }

	public function get_schema(){ return (array)self::RetrieveObject( $this->schemaKey ); }
	public function set_schema( $x ){ return $this->schemaKey = self::StoreObject( (object)$x ); }
	public function isset_schema(){ return !is_null( $this->schemaKey ); }

	public function get_query(){ return self::RetrieveObject( $this->qKey ); }
	public function set_query( $qObj ){ return $this->qKey = self::StoreObject( $qObj ); }
	public function isset_query(){ return !is_null( $this->qKey ) ; }

	protected function _filter( $filter ){
		$this->include( 'filter' );
		$this->filters[] = $filter;
		return !in_array( $filter, $this->filters, true )
			?	$this->filters[] = $filter
			:	false;
	}

	protected function _resolve_reqs(){
		foreach( $this->reqs as $reqField ){
			$this->query->fields->$reqField->ensure_field_availability( $this->name );
		}
	}

	protected function _release_reqs( $forWhat ){
		foreach( $this->reqs as $reqField ){
			$this->query->fields->$reqField->release_field_requirement( $this->name );
		}
	}

	protected function init_grouped( $spec ){
		return $this->grouped = !!$spec['grouped'];
	}

	protected function init_selectable( $spec ){
		if( $spec['selectable'] ){
			$this->selectable = true;
		}else{
			$this->selectable = false;
			$this->selected = false;
		}
	}

	protected function init_deselectable( $spec ){
		if( $spec['deselectable'] ){
			$this->deselectable = true;
		}else{
			$this->deselectable = false;
			$this->selectable = true;
			$this->selected = true;
		}
	}

	protected function init_selected( $spec ){
		return $this->selected = !!$spec['selected'];
	}

	protected function init_from( $spec ){
		if( !array_key_exists( 'from', $spec ) ){
			exit( __METHOD__ );
		}
		$this->from = array_key_exists( 'from', $spec ) && !empty( $spec['from'] )
			?	$spec['from']
			:	$this->query->from;
		$this->primary = $this->from===true || $this->from == $this->query->from;
	}

	protected function init_filters( $spec ){
		$filters = is_array( $spec['filters'] ) ? $spec['filters'] : [$spec['filters']];
		foreach( $filters as $filterIdx => $filter ){
			$this->filter( $filter );
		}
	}

	protected function init_groupable( $spec ){
		if( $spec['groupable'] ){
			$this->groupable = true;
		}else{
			$this->groupable = false;
			$this->grouped = false;
		}
	}

	protected function init_synonyms( $schema ){
		if( property_exists( $schema, 'synonyms' ) ){
			$this->synonyms = $schema->synonyms;
		}
	}
}
