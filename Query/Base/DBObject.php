<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base class for _Query and _Field
 * @author	Eric M. Roberts
 * @version	1.0
 */

class DBObject {

	public static $Registry = [];

	public function __get( $prop ){
		$m = "get_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m()
			:	(property_exists( $this, $prop )
				?	$this->$prop
				:	$this->throw_exception( __METHOD__, compact('prop','m') )
		);
	}

	public function __set( $prop, $value ){
		$m = "set_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m( $value )
			:	(property_exists( $this, $prop )
				?	$this->$prop = $value
				:	$this->throw_exception( __METHOD__, compact('prop','m') )
		);
	}

	public function __isset( $prop ){
		$m = "isset_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m()
			:	(property_exists( $this, $prop )
				?	isset( $this->$prop )
				:	$this->throw_exception( __METHOD__, compact('prop','m') )
		);
	}

	public function __unset( $prop ){
		$m = "set_{$prop}";
		return method_exists( $this, $m )
			?	$this->$m()
			:	(property_exists( $this, $prop )
				?	$this->prop = is_array($this->prop) ? [] : null
				:	$this->throw_exception( __METHOD__, compact('prop','m') )
		);
	}

	protected static function StoreObject( $object ){
		$id = spl_object_hash( $object );
		if( !array_key_exists( $id, self::$Registry ) ){
			self::$Registry[ $id ] = $object;
		}
		return $id;
	}

	protected static function RetrieveObject( $key ){
		return array_key_exists( $key, self::$Registry )
			?	self::$Registry[ $key ]
			:	$this->throw_exception( __METHOD__, compact( 'key' ), true );
	}

	protected function throw_exception( $method, $data=null, $die=true ){
		$cls = get_called_class();
		$d = print_r( $data, 1 );
		$ex = new Exception( "{$cls}::{$method} FAIL\n\n{$d}" );
		if( !$die ) return $ex;
		else exit( print_r( $ex, 1 ) );
	}

}
