<?php

namespace iceberg\hook;

use iceberg\hook\exceptions\InvalidHooksFileException;
use iceberg\hook\exceptions\HookNotFoundException;

class Hook {

	public static $enabled = true;

	public static $events = array();
	public static $hooks = array();
	public static $ignore = array();

	public static function loadFromFile($filePath) {

		$fileContent = @file_get_contents($filePath);
		if (!$fileContent) {
			throw new InvalidHooksFileException("Hook file at \"{$filePath}\" is not readable.");
		}

		$decoded = json_decode($fileContent);
		if (!$decoded) {
			throw new InvalidHooksFileException("Hook file at \"{$filePath}\" is not valid.");
		}

		foreach ($decoded as $name => $hook) {

			if (!$hook->path || !$hook->event) {
				throw new InvalidHooksFileException("Hook data file is incomplete for \"{$name}\".");
			}
			
			static::$hooks[$name] = $hook->path;
			static::$events[$hook->event][] = $name;
		}
	}

	public static function runEvent($event) {

		if (!static::$enabled) {
			return;
		}

		if (!array_key_exists($event, static::$events)) {
			return;
		}

		if (in_array($event, static::$ignore)) {
			return;
		}

		foreach (static::$events[$event] as $hook) {
			
			if (in_array($hook, static::$ignore)) {
				continue;
			}

			$runFile = static::$hooks[$hook];
			if (!file_exists($runFile)) {
				throw new HookNotFoundException("Hook script for \"{$hook}\" does not exist.");
			}

			shell_exec("sh {$runFile} 1>/dev/null 2>&1");
		}
	}

	public static function disable($hook) {
		static::$ignore[] = $hook;
	}

	public static function disableAll() {
		static::$enabled = false;
	}

	public static function enableAll() {
		static::$enabled = true;
	}

	public static function isEnabled() {
		return static::$enabled;
	}
	
}
