<?php

namespace commands;

use iceberg\hook\Hook;
use iceberg\config\Config;
use iceberg\cmd\AbstractCommand;
use iceberg\shell\ArgumentParser;
use iceberg\shell\OutputFormatter;

use iceberg\cmd\exceptions\InputDataNotFoundException;
use iceberg\cmd\exceptions\InputFileNotGivenException;

class Generate extends AbstractCommand {

	private static $post = array();

	public static function run() {
		$arguments = ArgumentParser::getInstance();

		static::$post["author"] = Config::getVal("general", "author");
		
		switch (true) {

			case $arguments->file_val:
				$inputFilePath = $arguments->file;
				break;
			
			case $arguments->name_val:
				$inputFilePath = str_replace("{article}", $arguments->name, Config::getVal("article", "source", true));
				break;
			
			default:
				throw new InputFileNotGivenException("Article file name or path was not given.");
				break;
		}
		
		$inputFileContent = @file_get_contents($inputFilePath);
		if (!$inputFileContent)
			throw new InputFileNotGivenException("Article file does not exist or could not be opened.");
		
		preg_match_all("/@([a-zA-Z]+):\s(.*)/", $inputFileContent, $metadata, PREG_SET_ORDER);
		foreach ($metadata as $detail)
			static::$post[$detail[1]] = $detail[2];

		foreach (array("title") as $required)
			if (!array_key_exists($required, static::$post))
				throw new InputDataNotFoundException("Required metadata \"$required\" not found.");

	}

}