<?php

namespace iceberg;

class NoAutoloaderException extends \Exception { }
class NamespaceNotSetException extends \Exception { }
class ClassNotFoundException extends \Exception { }

class Autoloader {

	private static $namespaces = array();
	private static $registered = false;

	public static function register() {

		if (static::$registered) return true;

		$set = spl_autoload_register(__CLASS__."::loadClass");

		if (!$set) {
			throw new NoAutoloaderException("Unable to register the Iceberg autoloader.");
		} else {
			static::$registered = true;
		}
		
		return true;
	}

	public static function addNamespace($namespace, $root) {

		if (!static::$registered) {
			static::register();
		}

		static::$namespaces[$namespace] = rtrim($root, DIRECTORY_SEPARATOR);
	}

	public static function loadClass($class) {

		$pathBits = explode("\\", $class);
		$namespace = $pathBits[0];
		
		if (!array_key_exists($namespace, static::$namespaces)) {
			throw new NamespaceNotSetException("Namespace source for \"{$pathBits[0]}\" has not been set.");
		}

		$path = static::$namespaces[$namespace]
		       .DIRECTORY_SEPARATOR
		       .implode(DIRECTORY_SEPARATOR, array_slice($pathBits, 1))
		       .".php";

		if (!file_exists($path)) {
			throw new ClassNotFoundException("Class file at path \"{$path}\" does not exist.");
		}
		include_once $path;
	}

}
