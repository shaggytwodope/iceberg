<?php

namespace iceberg\shell;

use iceberg\shell\exceptions\InvalidArgumentPassedException;
use iceberg\shell\exceptions\CommandArgumentNotGivenException;

class ArgumentParser {

	private $arguments = array();
	private static $object = false;

	public static function getInstance() {
		if (!static::$object)
			static::$object = new ArgumentParser();

		return static::$object;
	}

	private function __construct() {
		global $argv;
		global $argc;

		$parsedArguments = array();
		
		if ( $argc < 2 || substr($argv[1], 0, 2) == "--")
			throw new CommandArgumentNotGivenException("A command argument must be passed to use Iceberg.");

		for ($i = 2; $i < $argc; $i++) {
		
			if ( substr($argv[$i], 0, 2) == "--" ) {
				$keyword = str_replace("-", "_", substr($argv[$i], 2));
	
				if ( $i+1 != $argc && substr($argv[$i+1], 0, 2) != "--" )
					$value = $argv[++$i];
				else
					$value = true;
					
				$parsedArguments[$keyword] = $value;
			} else 
				throw new InvalidArgumentPassedException("Invalid argument \"{$argv[$i]}\" passed.");
		}

		$this->arguments = $parsedArguments;
	}
	
	public function __get($name) {
		
		if (substr($name, -4) != "_val") {
		
			if ( array_key_exists($name, $this->arguments) )
				return $this->arguments[$name];
			else 
				return false;
	
		} else {
			$name = substr($name, 0, -4);		

			if ( array_key_exists($name, $this->arguments) )	
				return is_string($this->arguments[$name]) ? $this->arguments[$name] : false;
			else 
				return false;
		}
	}

}

