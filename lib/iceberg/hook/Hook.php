<?php

namespace iceberg\hook;

use iceberg\hook\exceptions\HookNotFoundException;
use iceberg\hook\exceptions\HooksNamespaceNotSetException;

class Hook {

	private static $enabled = true;
	private static $namespace = false;
	
	public static function setEnabled($value = true) {
		static::$enabled = $value;
	}
	
	public static function setNamespace($namespace) {
		static::$namespace = $namespace;
	}
	
	public static function call($hook) {
		if (!static::$enabled)
			return false;
	
		if (!static::$namespace)
			throw new HooksNamespaceNotSetException("Hooks namespace was not set. Hooks not found.");
		
		$hook = str_replace("(hook)", ucfirst($hook), static::$namespace);
		$args = array_slice(func_get_args(), 1);
		
		$exists = @call_user_func("$hook::exists");
		if (!$exists)
			throw new HookNotFoundException("Hook \"$hook\" could not be found.");
		
		call_user_func("$hook::run", $args);
	}
	
}