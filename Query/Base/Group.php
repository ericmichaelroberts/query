<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Filter Clause Grouping Container
 * @author Eric M. Roberts
 * @version 1.0
 */

class Group extends DBObject {

	/* INSTANCE */
	public function __construct( $container=null, $logic=null ){
		$this->set_container( $container );
		if( $logic ){
			$this->set_logic( $logic );
		}
	}

	public function __get( $prop ){
		$m = "get_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m()
			:	(property_exists( $this, $prop )
				?	$this->$prop
				:	$this->throw_exception( __FUNCTION__, compact('prop','m') )
		);
	}
	public function __set( $prop, $value ){
		$m = "set_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m( $value )
			:	(property_exists( $this, $prop )
				?	$this->$prop = $value
				:	$this->throw_exception( __FUNCTION__, compact('prop','m') )
		);
	}
	public function __isset( $prop ){
		$m = "isset_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m()
			:	(property_exists( $this, $prop )
				?	isset( $this->$prop )
				:	$this->throw_exception( __FUNCTION__, compact('prop','m') )
		);
	}
	public function __unset( $prop ){
		$m = "set_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m()
			:	(property_exists( $this, $prop )
				?	$this->prop = is_array($this->prop) ? [] : null
				:	$this->throw_exception( __FUNCTION__, compact('prop','m') )
		);
	}

	public function __toString(){
		return $this->_logic == 'or'
			?	$this->render_or_group()
			:	$this->render_and_group();
	}

	protected $_k;
	protected $_logic;

	protected $children = [];
	protected $prodigals = [];

	public function get_clause(){
		return $this->_logic=='or'
			?	$this->render_or_group()
			:	$this->render_and_group();
	}

	public function get_references( &$collection=[] ){
		foreach( $this->children as $child ){
			if( is_object( $child ) ){
				$child->get_references( $collection );
			}elseif( !in_array( $child[0], $collection, true ) ){
				$collection[] = $child[0];
			}
		}

		foreach( $this->prodigals as $child ){
			if( is_object( $child ) ){
				$child->get_references( $collection );
			}elseif( !in_array( $child[0], $collection, true ) ){
				$collection[] = $child[0];
			}
		}

		return $collection;
	}

	/* GETTERS / SETTERS */
	public function unset_container( ){
		return $this->_k = null;
	}
	public function isset_container( ){
		return !is_null( $this->_k );
	}
	public function get_container( ){
		return !is_null( $this->_k )
			?	self::RetrieveObject( $this->_k )
			:	null;
	}
	public function set_container( $obj=null ){
		return $this->_k = !is_null( $obj )
			?	self::StoreObject( $obj )
			:	null;
	}

	public function get_outermost(){
		$x = $this;
		while(
			!is_null($x->container) &&
			is_a( $x->container, __CLASS__ )
		){
			$x = $x->container;
		}
		return $x;
	}
	public function is_outermost(){
		return is_null( $this->_k ) || !is_a( $this->container, __CLASS__ );
	}

	public function get_query(){
		$outermost = $this->get_outermost();
		return $outermost->container;
	}

	public function set_logic( $logic='and' ){
		return $this->_logic = strtolower( $logic );
	}
	public function get_logic(){
		return $this->_logic;
	}

	protected function append_child( $field, $filter ){
		$this->children[] = [ $field, $filter ];
		return $this;
	}


	/* PUBLIC FACING */
	public function filter( $x=null, $y=null ){
		return $this->append_child( $x, $y );
	}

	public function and( $field=null, $filter=null ){
		if( is_null( $this->_logic ) ){
			$this->_logic = 'and';
		}

		$m = !is_null( $filter )
			?	"{$this->_logic}_filter_and"
			:	( $field===true
				?	"{$this->_logic}_open_and"
				:	"{$this->_logic}_close_and"
		);

		return call_user_func( [ $this, $m ], $field, $filter );
	}

	public function or( $field=null, $filter=null ){
		if( is_null( $this->_logic ) ){
			$this->_logic = 'or';
		}

		$m = !is_null( $filter )
			?	"{$this->_logic}_filter_or"
			:	( $field===true
				?	"{$this->_logic}_open_or"
				:	"{$this->_logic}_close_or"
		);

		return call_user_func( [ $this, $m ], $field, $filter );
	}

	public function resolve(){
		$this->resolve_group();
		$this->resolve_children();
		$this->resolve_prodigals();
		$this->cleanup();
	}

	public function distribute( $strata, $hierarchy, $filteroids ){
		return $this->container->multilogic
			?	$this->distribute_multilogic( $strata, $hierarchy, $filteroids )
			:	$this->distribute_simple( $strata, $hierarchy, $filteroids );
	}

	protected function distribute_multilogic( $strata, $hierarchy, $filteroids ){

		// 1. get common level
		// 2. render references
		// 3. replace placeholders
		// 4. assign renderer to query-level-object...if possible
		$result = [];
		$fields = $this->get_filtered_fields();
		$common_indexes = null;

		if( sizeof( $strata ) > 1 ){

			foreach( $fields as $idx => $field ){
				$indexes = array_keys( $hierarchy->$field->chain );

				if( is_null( $common_indexes ) ){
					$common_indexes = $indexes;
				}else{
					$intersect = array_intersect( $common_indexes, $indexes );
					$common_indexes = $intersect;
				}

				$result[ $field ] = $hierarchy->$field;
			}

			$common_index = !empty( $common_indexes )
				?	max( $common_indexes )
				:	null;

		}

		if( !is_null( $common_index ) ){
			$this->unify_distributed_multiplex( $fields, $strata, $common_index );
		}else{
			$this->distribute_multiplexed_union( $fields, $strata, $hierarchy, $filteroids );
		}

		$clause = $this->get_clause();
	}

	protected function unify_distributed_multiplex( $fields, $strata, $max_common_index ){
		$clause = $this->get_clause();
		$queryFacade = $strata[ $max_common_index ];
		$queryKey = $queryFacade->object['key'];
		$queryObj = self::RetrieveObject( $queryKey );
		$fieldObjects = [];

		foreach( $fields as $field ){

			$fieldObj = $queryObj->fields->$field;
			$fieldObjects[ "{{$field}}" ] = substr( $fieldObj->refs, 0, 2 )=='->'
				?	$this->call_arrow_function( $fieldObj->refs )
				:	$fieldObj->refs;

			$fieldObj->filterable = true;
			$fieldObj->filterTarget = true;
			$fieldObj->filterFactor = true;
			$fieldObj->filter_to_subordinate = false;
			$fieldObj->include();
		}

		$clause = str_replace(
			array_keys( $fieldObjects ),
			array_values( $fieldObjects ),
			$clause
		);

		$queryObj->where = $clause;

	}

	protected function distribute_multiplexed_union( $fields, $strata, $hierarchy, $filteroids ){
		// Seriously Ugly, Complicated, Brain-Hurty, Convolution
	}

	protected function distribute_simple( $strata, $hierarchy, $filteroids ){
		$query = $this->query;
		foreach( $filteroids as $field => $filters ){
			foreach( $filters as $filter ){
				$query->fields->$field->filter( $filter );
			}
		}
	}

	protected function get_filtered_fields(){
		$output = [];
		$view = $this->get_clause();
		$pattern = '/(?<=\{)[a-z0-9_]+(?=\})/i';

		return preg_match_all( $pattern, $view, $matches )
			?	array_values( array_unique( $matches[0] ) )
			:	[];
	}

	protected function cleanup(){
		$this->children = array_filter( $this->children, [ $this, 'is_legit' ] );
		$this->prodigals = array_filter( $this->prodigals, [ $this, 'is_legit' ] );
	}

	protected function resolve_group( $asProdigal=false ){
		if( empty( $this->children ) ){
			$this->flip();
			if( empty( $this->prodigals ) ){
				if( !$this->is_outermost() ){
					$container = $this->container;
					if( !$asProdigal ){
						if( $this->logic == $container->logic ){
							$this->promote_children();
						}
					}
				}
			}
		}
	}

	protected function resolve_children(){
		startIterating:
		$children = array_values( $this->children );
		foreach( $children as $child ){
			if( is_object( $child ) ){
				$result = $child->resolve_child( $this->logic );
				if( $result ){
					goto startIterating;
				}
			}
		}
	}

	protected function resolve_child( $containerLogic ){
		$this->resolve_group();
		$this->resolve_children();
		$this->resolve_prodigals();
		$this->cleanup();
		if( !empty( $this->children ) ){
			if( $this->logic == $containerLogic && empty($this->prodigals) ){
				$this->promote_children();
				return true;
			}
		}
	}

	protected function resolve_prodigals(){
		startIterating:
		$children = array_values( $this->prodigals );
		foreach( $children as $child ){
			if( is_object( $child ) ){
				$result = $child->resolve_prodigal( $this->logic );
				if( $result ){
					goto startIterating;
				}
			}
		}
	}

	protected function resolve_prodigal( $containerLogic ){
		$this->resolve_group( true );
		$this->cleanup();
	}

	protected function get_groupspec(){
		return $this->logic == 'and'
			?	$this->optimize_and()
			:	$this->optimize_or();
	}

	protected function optimize_and(){
		$output = $this->optimize_and_children();
		if( !empty( $this->prodigals ) ){
			$output['OR'] = $this->optimize_and_prodigals();
		}
		return $output;
	}

	protected function optimize_or(){
		$output = !empty( $this->prodigals )
			?	$this->optimize_or_prodigals()
			:	[];
		$output['OR'] = $this->optimize_or_children();
		return $output;
	}

	protected function optimize_and_children(){
		$top = [];
		$and = [];

		foreach( $this->children as $idx => $child ){
			if( is_array( $child ) ){
				array_unshift( $top, json_encode( $child ) );
			}else{
				$child->logic=='and'
					?	array_unshift( $and, $child->groupspec )
					:	array_push( $and, $child->groupspec );
			}
		}

		return array_merge( $top, $and );
	}

	protected function optimize_and_prodigals(){
		$output = [];
		foreach( $this->prodigals as $idx => $child ){
			if( is_array( $child ) ){
				array_unshift( $output, json_encode( $child ) );
			}else{
				$child->logic=='and'
					?	array_unshift( $output, $child->groupspec )
					:	array_push( $output, $child->groupspec );
			}
		}

		return $output;
	}

	protected function optimize_or_children(){
		$output = [];
		foreach( $this->children as $idx => $child ){
			if( is_array( $child ) ){
				array_unshift( $output, json_encode( $child ) );
			}else{
				array_push( $output, $child->groupspec );
			}
		}
		return $output;
	}

	protected function optimize_or_prodigals(){
		$top = [];
		$and = [];

		foreach( $this->prodigals as $idx => $child ){
			if( is_array( $child ) ){
				array_unshift( $top, json_encode( $child ) );
			}else{
				$child->logic=='and'
					?	array_unshift( $and, $child->groupspec )
					:	array_push( $and, $child->groupspec );
			}
		}

		return array_merge( $top, $and );
	}

	/* AND ORIENTATION */
	protected function and_filter_and( $field, $filter ){
		return $this->append_child( $field, $filter );
	}

	protected function and_filter_or( $field, $filter ){
		$this->prodigals[] = [ $field, $filter ];
		return $this;
	}

	protected function and_open_and(){
		$klass = __CLASS__;
		$temp = new $klass( $this, 'and' );
		$this->children[] = $temp;
		return $temp;
	}

	protected function and_open_or(){
		$klass = __CLASS__;
		$temp = new $klass( $this, 'or' );
		$this->children[] = $temp;
		return $temp;
	}

	protected function and_close_and(){
		return $this->container;
	}

	protected function and_close_or(){
		$this->throw_exception( __METHOD__, 'cannot close-or from and-context' );
		return $this;
	}


	/* OR ANDIENTATION */
	protected function or_filter_and( $field, $filter ){
		$this->prodigals[] = [ $field, $filter ];
		return $this;
	}

	protected function or_filter_or( $field, $filter ){
		return $this->append_child( $field, $filter );
	}

	protected function or_open_and(){
		$klass = __CLASS__;
		$temp = new $klass( $this, 'and' );
		$this->children[] = $temp;
		return $temp;
	}

	protected function or_open_or(){
		$klass = __CLASS__;
		$temp = new $klass( $this, 'or' );
		$this->children[] = $temp;
		return $temp;
	}

	protected function or_close_and(){
		$this->throw_exception( __METHOD__, 'cannot close-and from or-context' );
		return $this;
	}

	protected function or_close_or(){
		return $this->container;
	}


	/* RENDERING */
	protected function render( $items, $ploder ){
		$output = [];
		foreach( array_unique( $items ) as $child ){
			$output[] = is_array( $child )
				?	"{{$child[0]}} {$child[1]}"
				:	(string)$child;
		}
		$result = implode( " {$ploder} ", $output );
		return sizeof( $output ) > 1 ? "({$result})" : $result;
	}

	protected function render_and_group(){
		//$children = $this->render( $this->children, 'AND' );
		$children = $this->render( $this->children, 'AND' );
		if( !empty( $this->prodigals ) ){
			$prodigals = $this->render( $this->prodigals, 'OR' );
			return "{$children} OR {$prodigals}";
		}
		return $children;
	}

	protected function render_or_group(){
		$children = $this->render( $this->children, 'OR' );
		if( !empty( $this->prodigals ) ){
			$prodigals = $this->render( $this->prodigals, 'AND' );
			return "({$prodigals} AND {$children})";
		}
		return $children;
	}


	/* MISCELLANEOUS */
	protected function promote_children(){
		$container = $this->container;
		while( !empty( $this->children ) ){
			$child = array_shift( $this->children );
			if( is_object( $child ) ){
				$child->container = $container;
			}
			$container->children[] = $child;
		}
	}

	protected function flip(){
		$this->_logic = $this->_logic=='and' ? 'or' : 'and';
		$children = $this->children;
		$prodigals = $this->prodigals;
		$this->children = $prodigals;
		$this->prodigals = $children;
	}

	protected function throw_exception( $method, $data=null, $die=true ){
		$cls = get_called_class();
		$d = print_r( $data, 1 );
		$ex = new Exception( "{$cls}::{$method} FAIL\n\n{$d}" );
		if( !$die ) return $ex;
		else exit( print_r( $ex, 1 ) );
	}

	protected function is_legit( $item ){
		return 	(is_array( $item ) && !empty( $item ))
				||
				(!empty($item->children) || !empty($item->prodigals));
	}
}
