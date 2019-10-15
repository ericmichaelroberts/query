<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'libraries/Query/Base/Query.php');

/**
 * DMSA_Query Package Autoloader
 * @author	Eric M. Roberts
 * @version	1.0
 */

if(!defined('QUERY_AUTOLOAD_REGISTERED')){
	spl_autoload_register(function($class){
		if(preg_match('/_(Query)$/',$class)){
			$dir = APPPATH.'libraries/Query/';
			$files = scandir($dir);
			$filename = "{$class}.php";
			$target = "{$dir}{$filename}";
			if(in_array($filename,$files)){
				include($target);
			}else{
				$index = array_search( $filename, $files );
				exit( print_r(	compact('dir','files','filename','target','index'), 1 )	);
			}
		}
	});
	define('QUERY_AUTOLOAD_REGISTERED',true);
}
