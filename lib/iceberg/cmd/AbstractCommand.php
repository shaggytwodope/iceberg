<?php

namespace iceberg\cmd;

abstract class AbstractCommand {

	public static $log = array();
	
	final public static function exists() { return true; }
	
	abstract public static function run();

	public static function output() {
		echo PHP_EOL;
		foreach (static::$log as $log) echo $log, PHP_EOL;
		echo PHP_EOL;
	}

}