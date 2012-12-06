<?php

namespace iceberg\cmd;

use iceberg\hook\Hook;
use iceberg\ClassNotFoundException;
use iceberg\cmd\exceptions\CommandNamespaceNotSetException;
use iceberg\cmd\exceptions\CommandDoesNotExistException;

class Command {

	private static $namespace = false;
	
	public static function setNamespace($namespace) {

		static::$namespace = $namespace;
	}
	
	public static function call($command) {

		$command = strtolower(trim($command));

		if (!static::$namespace) {
			throw new CommandNamespaceNotSetException("Command namespace was not set. Command not found.");
		}
		
		$call = str_replace("{command}", ucfirst($command), static::$namespace);
		$args = array_slice(func_get_args(), 1);
		
		try {
			@call_user_func("$call::exists");
		} catch (ClassNotFoundException $e) {
			throw new CommandDoesNotExistException("Command \"{$command}\" does not exist.");
		}

		Hook::runEvent("pre-{$command}");
		call_user_func("$call::run", $args);
		Hook::runEvent("post-{$command}");
	}

}
