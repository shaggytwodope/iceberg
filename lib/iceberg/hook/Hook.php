<?php

namespace iceberg\hook;

use iceberg\config\Config;
use iceberg\shell\ArgumentParser;
use iceberg\hook\exceptions\HookNotFoundException;
use iceberg\hook\exceptions\InvalidHooksFileException;

class Object { }
class Hook {

	public static $enabled = true;

	public static $events = array(), $hooks = array(), $env = array();

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

			$hook->enabled = true;
			$hook->output = (isset($hook->output) && !$hook->output) ? false : true;
			$hook->data = ($hook->data) ? (array) $hook->data : array();

			static::$hooks[$name] = $hook;
			static::$events[$hook->event][] = &static::$hooks[$name];
		}
	}

	public static function runEvent($event) {

		if (!static::$enabled || !array_key_exists($event, static::$events)) {
			return;
		}

		$arguments = ArgumentParser::getInstance();

		foreach (static::$events[$event] as $hook) {
			
			if (!$hook->enabled) {
				continue;
			}

			if (!file_exists($hook->path)) {
				throw new HookNotFoundException("Hook script for \"{$hook->name}\" does not exist.");
			}

			$hookArguments = array();
			foreach ($hook->data as $dataPiece) {
				// I'll do stuff here later :3
			}

			$hookArgumentString = implode(" ", $hookArguments);
			$execOutput = shell_exec("sh {$hook->path} {$hookArgumentString}");

			if ($hook->output) {
				echo $execOutput;
			}
		}
	}

	public static function setEnvironmentData($dataName, $dataContent) {

		if (is_object($dataContent)) {
			$dataContent = (array) $dataContent;
		}

		static::$env[$dataName] = $dataContent;
	}

	public static function disableHook($hook) {

		if (!array_key_exists($hook, static::$hooks)) {
			return;
		}

		static::$hooks[$hook]->enabled = false;
	}

	public static function enableSystem() {
		static::$enabled = true;
	}

	public static function disableSystem() {
		static::$enabled = false;
	}

	public static function enabled() {
		return static::$enabled;
	}

}
