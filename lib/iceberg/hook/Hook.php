<?php

namespace iceberg\hook;

use iceberg\ClassNotFoundException as NoClass;
use iceberg\hook\exceptions\HookNotFoundException;
use iceberg\hook\exceptions\HooksNamespaceNotSetException;

class Hook {

	private static $enabled = true;
	private static $namespace = false;
	private static $data = array();
	
	public static function setEnabled($value = true) {
		static::$enabled = $value;
	}
	
	public static function setNamespace($namespace) {
		static::$namespace = $namespace;
	}
	
	public static function setData($hook, $array) {
		static::$data[$hook] = $array;
	}
	
	public static function call($hook, $exception = false) {
		if (!static::$enabled)
			return false;
	
		if (!static::$namespace)
			throw new HooksNamespaceNotSetException("Hooks namespace was not set. Hooks not found.");

		if ( array_key_exists($hook, static::$data) ) {
			$args = static::$data[$hook];
		} else {
			$args = array();
		}
		
		$hook = str_replace("{hook}", $hook, static::$namespace);
		
		try {
			@call_user_func("$hook::exists");
		} catch (NoClass $e) {
			if ($exception)
				throw new HookNotFoundException("Hook \"$hook\" could not be found.");
			else 
				return;
		}
		
		call_user_func_array("$hook::run", $args);
	}
	
}