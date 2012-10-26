<?php

namespace iceberg\cmd;

abstract class AbstractCommand {
	
	final public static function exists() { return true; }
	
	abstract public static function run();

}