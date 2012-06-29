<?php

namespace iceberg\hook;

abstract class AbstractHook {

	public static $rootDir = false;

	public static function exists() { return true; }
	public static function run() {}

}