<?php

namespace hook;

use iceberg\hook\AbstractShellHook;

class PostGenerateHook extends AbstractShellHook {

	protected static $path = "";
	protected static $command = "";
	
	public static function prepare($data) {
		static::$path = ROOT_DIR."output";
		static::$command = "mkdir hook-{$data['post']['info']['slug']}";
	}

}