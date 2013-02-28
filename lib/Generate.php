<?php

namespace commands;

use SQLite3;
use stdClass;
use DateTime;
use DateTimeZone;

use Twig_Environment;
use Twig_SimpleFunction;
use Twig_Loader_Filesystem;
use Twig_Extension_Variables;

use iceberg\hook\Hook;
use iceberg\config\Config;
use iceberg\cmd\AbstractCommand;
use iceberg\shell\ArgumentParser;

use iceberg\cmd\exceptions\InvalidInputException;
use iceberg\cmd\exceptions\InputDataNotFoundException;
use iceberg\cmd\exceptions\InputFileNotGivenException;

use iceberg\layout\exceptions\LayoutNotGivenException;
use iceberg\layout\exceptions\LayoutFileDoesNotExistException;

class Object extends stdClass { }
class Generate extends AbstractCommand {

	public static function run() {

		$arguments = ArgumentParser::getInstance();

		$database = new SQLite3(Config::getVal("general", "database", true));
		
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
			$article->{$detail[1]} = trim($detail[2]);
		}

		if (!isset($article->uid)) {
			throw new InputDataNotFoundException("Article does not have a unique ID.");
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

			case $arguments->layout_val:
				$layoutFile = $argument->layout;
				break;

			case isset($article->layout):
				$layoutFile = $article->layout;
				break;

			default:
				throw new LayoutNotGivenException("Layout to be used was not given.");
				break;
		}

		$layoutFilePath = str_replace("{layout}", $layoutFile, Config::getVal("article", "layout", true));
		if (!file_exists($layoutFilePath)) {
			throw new LayoutFileDoesNotExistException("Layout file \"{$layoutFilePath}\" does not exist.");
		}

		$variables = new Twig_Extension_Variables;
		$variables->register("_output");
		$variables->register("_reload");

		$layoutLoader = new Twig_Loader_Filesystem(dirname($layoutFilePath));
		$layoutParser = new Twig_Environment($layoutLoader);
		$layoutParser->addExtension($variables);

		$layoutData = array(
			"articles" => (array) $article,
			"config" => Config::_getValues(),
		);

		$layoutRendered = $layoutParser->render(basename($layoutFilePath), $layoutData);

		$outputFilePathRoot = Config::getVal("article", "output", true);

		switch (true) {

			case $arguments->output_val:
				$outputFilePath = $arguments->output;
				break;

			case isset($article->output):
				$outputFilePath = $article->output;
				break;

			default:
				if (!isset($variables->_output)) {
					throw new InvalidInputException("Output path for template \"{$layoutFile}\" was not defined.");
				}

				$outputFilePath = $outputFilePathRoot.str_replace("{slug}", $article->slug, $variables->_output);
				break;
		}
		@mkdir(dirname($outputFilePath), 0777, true);

		$outputFileWritten = @file_put_contents($outputFilePath, $layoutRendered);
		if (!$outputFileWritten) {
			throw new InvalidInputException("Could not write generated output for \"{$article->title}\" to file.");
		}

		$layoutFilePath = basename($layoutFilePath);
		echo "=> Generated \"{$layoutFilePath}\" at path \"{$outputFilePath}\"\n";

		if (isset($variables->_reload) && is_array($variables->_reload)) {

			foreach ($variables->_reload as $file) {

				if (!array_key_exists("layout", $file) || !array_key_exists("output", $file)) {
					throw new InvalidInputException("Invalid or missing layout reloading data in \"{$layoutFilePath}\".");
				}

				$layoutFilePath = $file["layout"];
				$outputFilePath = $outputFilePathRoot.$file["output"];

				$layoutRendered = $layoutParser->render($layoutFilePath, $layoutData);

				$outputFileWritten = @file_put_contents($outputFilePath, $layoutRendered);
				if (!$outputFileWritten) {
					throw new InvalidInputException("Could not write generated output for \"{$layoutFilePath}\" to file.");
				}

				echo "=> Generated \"{$layoutFilePath}\" at path \"{$outputFilePath}\"\n";
			}
		}

		Hook::setEnvironmentData("article", $article);
	}

}
