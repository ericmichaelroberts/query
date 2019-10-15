<?php defined('BASEPATH') OR exit('No direct script access allowed');


trait RenderTraits {

	protected function get_indents( $base=0 ){
		$baseIndents = str_repeat( "\t", $base );
		$chainIndents = !is_null( $this->parent_query )
			?	$this->parent_query->get_indents( 1 )
			:	"";
		return "{$baseIndents}{$chainIndents}";
	}

	public function build(){
		if( is_null( $this->pKey ) ){
			$this->assemble_logic();
		}

		if( is_object( $this->from ) ){
			$this->economize_subsource();
		}

		return $this->build_query();
	}

	protected function build_query(){
		foreach( $this->blueprint as $row ){
			if( is_array( $row ) ){
				$call = array_shift( $row );
				$args = sizeof( $row )
					?	array_shift( $row )
					:	[];
				$output[] = property_exists( $this->_methods, $call )
					?	call_user_func_array( $this->_methods->$call, $args )
					:	call_user_func_array( [ $this, $call ], $args );
			}else $output[] = $row;
		}

		$rendered_output = str_replace( "  ", " ", implode( "\n", array_filter($output) ) );

		return $rendered_output;
	}

	protected function build_strata(){
		$subject = $this;
		$chain = [];

		do{
			$chain[] = $subject->profile();
			$nextFrom = $subject->get_from();
			$subject = $nextFrom;
		}while( !is_string( $subject ) );

		return $chain;
	}

	protected function build_hierarchies(){
		$output = new stdClass;
		foreach( (array)$this->fields as $fieldName => $fieldObject ){
			$output->$fieldName = $this->build_hierarchy( $fieldName );
		}
		return $output;
	}

	protected function build_hierarchy( $field ){
		$temp = new stdClass;
		$temp->chain = [];
		$temp->filterable = [];
		$temp->targetable = [];
		$temp->fts = [];

		$subject = $this->fields->$field;

		do{
			$temp->chain[] = $subject;
			$temp->filterable[] = $subject->filterable ? 1 : 0;
			$temp->targetable[] = $subject->filterTarget ? 1 : 0;
			$temp->fts[] = $subject->filter_to_subordinate ? 1 : 0;
			$subordinate = $subject->subordinate;
			if( !is_null( $subordinate ) ){
				$subject = $subordinate;
			}
		}while( !is_null( $subordinate ) );

		return $temp;
	}

	protected function build_logic(){
		if( $this == $this->outermost ){

			if( !empty( $this->filters ) ){

				$this->logical_groups = new _Group( $this );
				$this->current_group = $this->logical_groups;

				foreach( $this->filters as $idx => $data ){
					list( $method, $args ) = $data;
					$this->current_group = call_user_func_array(
						[ $this->current_group, $method ],
						$args
					);
				}

				$logical_groups = $this->logical_groups;

				return null;
			}
		}
	}

	protected function build_selection(){
		$distinction = $this->distinct ? " DISTINCT" : "";
		$indents = $this->get_indents();

		$middle = $this->calc_found_rows && !$this->is_subquery()
			?	'SELECT SQL_CALC_FOUND_ROWS'
			:	'SELECT';
		$head = "{$indents}{$middle}{$distinction}";

		$output = [];

		$fieldKeys = array_keys((array)$this->fields);



		foreach( $fieldKeys as $fieldIdx => $fieldKey ){
			$fieldObject = $this->fields->$fieldKey;



			if( $fieldObject->selected ){

				$refs = substr( $fieldObject->refs, 0, 2 )=='->'
					?	$this->call_refs_function( $fieldObject->refs )
					:	$fieldObject->refs;
				$alias = $fieldObject->alias ? " AS {$fieldObject->alias}" : "";
				$str = trim("{$refs} {$alias}");

				$output[] = "\t{$indents}{$str}";
			}
		}

		$selection = sizeof( $output )
			?	implode( ",\n", $output )
			:	'*';

		return "{$head}\n{$selection}";
	}

	protected function build_from(){
		$indents = $this->get_indents();

		$fromSource = is_string( $this->from )
			?	( substr( $this->from, 0, 2 )=='->'
				?	$this->call_from_function( $this->from )
				:	$this->from)
			:	$this->from->build();

		return is_string( $this->from )
			?	"{$indents}FROM {$fromSource} {$this->alias}"
			:	"{$indents}FROM (\n{$fromSource}\n{$indents}) {$this->alias}";
	}

	protected function build_index(){
		$indents = $this->get_indents();

		return is_null( $this->index )
			?	null
			:	"{$indents}{$this->index}";
	}

	protected function build_joins(){
		$output = [];
		$indents = $this->get_indents();

		foreach( $this->joins as $join => $joinSpec ){
			if( $joinSpec->joined ){
				$output[$join] = "{$indents}{$joinSpec->clause}";
			}
		}

		return implode( "\n", $output );
	}

	protected function build_filters(){

		$indents = $this->get_indents();

		$where = $this->where;
		$base_filters = $this->build_base_filters();

		$filters[] = $base_filters;
		$filters[] = $where;

		$filters = array_filter( $filters );

		$combined = implode( ' AND ', $filters );

		return !empty( $filters )
			?	"{$indents}WHERE {$combined}"
			:	null;
	}

	protected function build_base_filters(){
		$output = [];

		foreach( $this->fields as $field => $fieldObject ){
			if( $fieldObject->filterable && !empty($fieldObject->filters) && !$fieldObject->having ){
				foreach( $fieldObject->filters as $filterIdx => $filter ){
					$output[] = is_array( $filter )
						?	$this->{$filter[0]}()
						:	( substr( $filter, 0, 2 )=='->'
							?	$this->call_filter_function( $fieldObject->refs, $filter )
							:	"{$fieldObject->refs} {$filter}"
					);
				}
				$fieldObject->include( 'filter' );
			}
		}

		$output = array_unique( $output );

		$cnds = array_filter( $output );

		return sizeof( $cnds ) ? implode( ' AND ', array_filter( $output ) ) : null;
	}

	protected function build_having(){
		$output = [];
		$indents = $this->get_indents();

		foreach( $this->fields as $field => $fieldObject ){
			if( $fieldObject->filterable && !empty($fieldObject->filters) && $fieldObject->having ){
				foreach( $fieldObject->filters as $filterIdx => $filter ){
					$output[] = is_array( $filter )
						?	$this->{$filter[0]}()
						:	"{$fieldObject->refs} {$filter}";
				}
			}
		}
		$cnds = implode( ') AND (', array_filter( $output ) );
		return sizeof($output)
			?	"{$indents}HAVING ({$cnds})"
			:	"";
	}

	protected function build_group(){

		$output = [];
		$indents = $this->get_indents();

		foreach( $this->fields as $field => $fieldObject ){
			if( $fieldObject->groupable && $fieldObject->grouped ){
				$output[$field] = $fieldObject->grouping_idx;
			}
		}

		arsort($output);

		$fields = array_keys($output);
		$groups = implode( ', ', $fields );
		$wru = $this->rollup ? " WITH ROLLUP" : "";

		return sizeof( $output )
			?	"{$indents}GROUP BY {$groups}{$wru}"
			:	"";
	}

	protected function build_order(){
		$output = [];
		$indents = $this->get_indents();

		if( !$this->rollup ){
			foreach( $this->order as $field => $dir ){
				$output[] = "{$field} {$dir}";
			}
			$order = implode( ', ', $output );
		}

		return sizeof($output)
			?	"{$indents}ORDER BY {$order}"
			:	"";
	}

	protected function build_limit(){
		$indents = $this->get_indents();
		return is_null( $this->limit ) ? "" : "{$indents}LIMIT {$this->offset},{$this->limit}";
	}


}
