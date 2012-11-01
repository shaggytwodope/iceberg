<?php

namespace commands;

use iceberg\hook\Hook;
use iceberg\config\Config;
use iceberg\cmd\AbstractCommand;
use iceberg\shell\ArgumentParser;
use iceberg\shell\OutputFormatter;

use iceberg\cmd\exceptions\InvalidInputException;
use iceberg\cmd\exceptions\InputDataNotFoundException;
use iceberg\cmd\exceptions\InputFileNotGivenException;

class Generate extends AbstractCommand {

	private static $post = array();

	public static function run() {
		$arguments = ArgumentParser::getInstance();
		
		switch (true) {

			case $arguments->file_val:
				$inputFilePath = $arguments->file;
				break;
			
			case $arguments->name_val:
				$inputFilePath = str_replace("{article}", $arguments->name, Config::getVal("article", "input", true));
				break;
			
			default:
				throw new InputFileNotGivenException("Article file name or path was not given.");
				break;
		}

		switch (true) {

			case $arguments->output_val:
				$outputFilePath = $arguments->output;
				break;

			case array_key_exists("output", static::$post):
				$outputFilePath = static::$post["output"];
				break;

			default:
				$outputFilePath = str_replace("{slug}", static::$post["slug"], Config::getVal("article", "output", true));
				break;
		}
		
		$inputFileContent = @file_get_contents($inputFilePath);
		if (!$inputFileContent)
			throw new InputFileNotGivenException("Article file does not exist or could not be opened.");

		static::$post["author"] = Config::getVal("general", "author", true);
		static::$post["content"] = trim(preg_replace("/@([a-zA-Z]+):\s(.*)\n?/", "", $inputFileContent));
		
		preg_match_all("/@([a-zA-Z]+):\s(.*)\n?/", $inputFileContent, $metadata, PREG_SET_ORDER);
		foreach ($metadata as $detail)
			static::$post[$detail[1]] = $detail[2];

		foreach (array("title") as $required)
			if (!array_key_exists($required, static::$post))
				throw new InputDataNotFoundException("Required metadata \"{$required}\" not found.");

		if ( !array_key_exists("slug", static::$post) )
				static::$post["slug"] = trim(static::$post["title"]);
		static::$post["slug"] = strtolower(str_replace(" ", "-", preg_replace("/[^a-zA-Z0-9_\s]/", "", static::$post["slug"])));

		if (array_key_exists("date", static::$post)) {

			$timestamp = strtotime(static::$post["date"]);
			if (!$timestamp)
				throw new InvalidInputException("Invalid date metadata. Make sure the date is a valid PHP date.");

			static::$post["date"] = new \DateTime("@$timestamp");
			static::$post["date"]->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		} else
			static::$post["date"] = new DateTime();

		$outputFileDirectory = dirname($outputFilePath);
		@mkdir($outputFileDirectory, 0777, true);

		file_put_contents($outputFilePath, static::$post["content"]);
	}

}