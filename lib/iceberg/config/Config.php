<?php

namespace iceberg\config;

use iceberg\config\exceptions\ConfigFileNotFoundException;
use iceberg\config\exceptions\InvalidConfigFileException;
use iceberg\config\exceptions\UnknownConfigValueException;

class Config {

	private static $values = array();

	public static function loadFromFile($path) {

		if (!file_exists($path)) {
			throw new ConfigFileNotFoundException("Config file \"{$path}\" not found.");
		}

		$configData = file_get_contents($path);
		
		$parsedConfig = parse_ini_string($configData, true);
		if (!$parsedConfig) {
			throw new InvalidConfigFileException("Config file \"{$path}\" is invalid or corrupted.");
		}

		static::$values = $parsedConfig;
	}

	public static function setVal($group, $key, $val) {
		static::$values[$group][$key] = $val;
	}

	public static function getVal($group, $key, $exception = false) {

		if (!isset(static::$values[$group][$key])) {
			if ($exception) {
				throw new UnknownConfigValueException("Required config value \"{$group}.{$key}\" not found.");
			} else  {
				return false;
			}
		}

		return static::$values[$group][$key];
	}

	public static function _getValues() {
		return static::$values;
	}

}
