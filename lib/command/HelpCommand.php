<?php

namespace command;

use iceberg\command\AbstractCommand;

class HelpCommand extends AbstractCommand {

	public static function run($args = array()) {
		echo "usage: iceberg generate <article-name> [--no-hook]\n",
		     "               generate --all [--no-hook]\n"; 
	}

}