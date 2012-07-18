<?php

namespace commands;

use iceberg\config\Config;
use iceberg\cmd\AbstractCommand;
use iceberg\shell\ArgumentParser;

use iceberg\cmd\exceptions\InputFileNotGivenException;

class Generate extends AbstractCommand {

	private static $post = array();

	public static function run() {
		$arguments = ArgumentParser::getInstance();
		
		if ($arguments->file_val) {
			$path = $arguments->file;
		} else if ($arguments->name_val) {
			$path = str_replace("{article}", $arguments->name, Config::getVal("article", "source", true));
		} else {
			throw new InputFileNotGivenException("Article file name or path was not given.");
		}
		
		preg_match_all("/@([a-zA-Z]+):\s(.*)/", file_get_contents($path), $metadata, PREG_SET_ORDER);
		foreach ($metadata as $detail) {
			static::$post[$detail[1]] = $detail[2];
		}
		
		if ($arguments->author_val)
			static::$post["author"] = $arguments->author_val;
		
		if ($arguments->title_val)
			static::$post["title"] = $arguments->title_val;
		
		if ($arguments->layout_val)
			static::$post["layout"] = $arguments->layout_val;

	}

}