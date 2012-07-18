<?php

namespace iceberg\cmd;

abstract class AbstractCommand {

	public static $log = array();
	
	final public static function exists() { return true; }
	
	abstract public static function run();

}