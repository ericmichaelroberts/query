<?php defined('BASEPATH') OR exit('No direct script access allowed');


trait QueryTraits {

	protected function initialize_schema( $schema ){
		$this->schema = $schema;
		$this->fields = new stdClass;
		foreach( ['methods','from','alias','index','rollup','distinct','rowcount','joins','fields','order','having'] as $prop ){
			$m = "initialize_{$prop}";
			method_exists( $this, $m )
				?	$this->$m( $schema )
				:	$this->$prop = (
						array_key_exists( $prop, $schema )
							?	$schema[$prop]
							:	null);
		}
	}

	protected function initialize_methods( $schema ){
		if( array_key_exists( 'methods', $schema ) ){
			foreach( $schema['methods'] as $name => $methodArray ){

				$args = is_array( $methodArray ) && array_key_exists( 'args', $methodArray )
					?	(is_array( $methodArray['args'] )
							?	implode( ',', $methodArray['args'] )
							:	$methodArray['args'])
					:	'';

				$fnSpec = is_array( $methodArray )
					?	(array_key_exists( 'function', $methodArray )
						?	$methodArray['function']
						:	$methodArray)
					:	$methodArray;

				$innerFn = is_string( $fnSpec ) ? $fnSpec : implode( "\n", $fnSpec );
				$outerFn = "\$closure=function({$args}){{$innerFn}};\$closure=\$closure->bindTo(\$that);return \$closure;";
				$factory = eval("return function(\$that){ {$outerFn} };");

				$this->_methods->$name = $factory( $this );
			}
		}
	}

	protected function initialize_from( $schema ){
		return $this->from = is_array( $schema['from'] )
			?	$this->initialize_interior_object( $schema['from'], 'from' )
			:	$schema['from'];
	}

	protected function initialize_interior_object( $spec, $entity ){
		switch( true ){
			case is_string( $spec['object'] ):
				$obj = new $spec['object']( $this );
			break;

			case is_array( $spec['object'] ):
				$obj = new Query( $this );
				$obj->initialize( $spec['object'] );
			break;

			default:
				return $this->throw_exception( __METHOD__, 'object must be string or array' );
			break;
		}

		if( array_key_exists( 'schema', $spec ) ){
			$obj->apply_initialization_params( $spec['schema'] );
		}

		return $obj;
	}

	protected function initialize_fields( $schema ){
		$from = $schema['from'];
		$defaultFrom = is_object( $schema['from'] ) ? true : $schema['from'];

		foreach( $schema['fields'] as $x => $y ){
			list( $field, $spec ) = is_int( $x )
				?	[ $y, null ]
				:	[ $x, $y ];

			$fullSpec = $this->build_field( $field, $spec );

			$field = $fullSpec['name'];

			$this->fields->$field = new QueryField( $this, $field, $fullSpec );
		}
	}

	protected function initialize_rowcount( $schema ){
		return $this->calc_found_rows = isset( $schema['rowcount'] ) && !!$schema['rowcount'];
	}

	protected function initialize_joins( $schema ){
		if( array_key_exists( 'joins', $schema ) ){
			foreach( $schema['joins'] as $table => $spec ){

				$tmp = new stdClass;

				$tmp->clause = $spec[0];
				$tmp->dep_fields = $spec[1];
				$tmp->constituents = [];
				$tmp->joined = false;

				$this->joins->$table = $tmp;
			}
		}
	}

	protected function initialize_order( $schema ){
		if( array_key_exists( 'order', $schema ) ){
			$this->order = $schema['order'];
		}
	}



}
