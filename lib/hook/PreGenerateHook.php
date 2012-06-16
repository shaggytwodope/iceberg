<?php

namespace hook;

use iceberg\hook\AbstractCodeHook;

class PreGenerateHook extends AbstractCodeHook {

	public static function run() {
		echo "The pre-generate hook was run! (It's harmless).", PHP_EOL;
	}

}