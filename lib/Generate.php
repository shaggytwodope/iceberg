<?php

namespace commands;

use DateTime;
use DateTimeZone;

use iceberg\config\Config;
use iceberg\cmd\AbstractCommand;
use iceberg\shell\ArgumentParser;

use iceberg\cmd\exceptions\InvalidInputException;
use iceberg\cmd\exceptions\InputDataNotFoundException;
use iceberg\cmd\exceptions\InputFileNotGivenException;

class Object { }
class Generate extends AbstractCommand {

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
		
		$inputFileContent = @file_get_contents($inputFilePath);
		if (!$inputFileContent) {
			throw new InputFileNotGivenException("Article file does not exist or could not be opened.");
		}

		$article = new Object;
		$article->author = Config::getVal("general", "author", true);
		$article->content = trim(preg_replace("/@([a-zA-Z]+):\s(.*)\n?/", "", $inputFileContent));

		preg_match_all("/@([a-zA-Z]+):\s(.*)\n?/", $inputFileContent, $metadata, PREG_SET_ORDER);
		foreach ($metadata as $detail) {
			$article->$detail[1] = trim($detail[2]);
		}

		if (!isset($article->title)) {
			throw new InputDataNotFoundException("Article does not have a title.");
		}

		if (!isset($article->slug)) {
			$article->slug = strtolower($article->title);
		}
		$article->slug = str_replace(" ", "-", preg_replace("/[^a-zA-Z0-9_\s]/", "", $article->slug));

		if (isset($article->date)) {
			$timestamp = strtotime($article->date);
			if (!$timestamp) {
				throw new InvalidInputException("Invalid article date. Make sure the date is a valid PHP date.");
			}
			$article->date = new DateTime("@{$timestamp}");
		} else {
			$article->date = new DateTime();
		}
		$article->date->setTimezone(new DateTimeZone(date_default_timezone_get()));

		switch (true) {

			case $arguments->output_val:
				$outputFilePath = $arguments->output;
				break;

			case isset($tempPost->output):
				$outputFilePath = $article->output;
				break;

			default:
				$outputFilePath = str_replace("{slug}", $article->slug, Config::getVal("article", "output", true));
				break;
		}
		@mkdir(dirname($outputFilePath), 0777, true);
	}

}
