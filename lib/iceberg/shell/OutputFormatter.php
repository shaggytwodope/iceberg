<?php

namespace iceberg\shell;

use iceberg\shell\exceptions\InvalidOutputColourException;
use iceberg\shell\exceptions\InvalidOutputStringException;

class OutputFormatter {

	public static $colours = array(
		"black" => "0;30", 
		"white" => "1;37",
		"gray" => "1;30", 
		"lightGray" => "0;37",
		"blue" => "0;34", 
		"lightBlue" => "1;34",
		"green" => "0;32", 
		"lightGreen" => "1;32",
		"cyan" => "0;36", 
		"lightCyan" => "1;36",
		"red" => "0;31", 
		"lightRed" => "1;31",
		"purple" => "0;35", 
		"lightPurple" => "1;35",
		"brown" => "0;33",
		"yellow" => "1;33"
	);
	
	public static function __callStatic($name, $arguments) {
	
		if ( !array_key_exists(0, $arguments) || !is_string($arguments[0]) )
			throw new InvalidOutputStringException("Formatted output must be a valid string.");
	
		if ( !array_key_exists($name, static::$colours) )
			throw new InvalidOutputColourException("Output colour \"{$name}\" does not exist.");

		$workString = "\033[".static::$colours[$name]."m";
		$closeLength = 1;
			
		if ( in_array("underline", $arguments) ) {
			$workString .= "\033[4m";
			$closeLength++;
		}
		
		$workString .= $arguments[0];
		
		for ($i = 0; $i < $closeLength; $i++)
			$workString .= "\033[0m";
		
		return $workString;	
	}

}