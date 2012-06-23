<?php

namespace iceberg;

class ErrorHandler {

	private static $registered = false;
	private static $logPath = false;
	
	public static function register($logPath = false) {
		if (!static::$registered)
			static::$registered = set_exception_handler(__CLASS__."::exceptionHandler");
	
		if (!$logPath)
			static::$logPath = false;

		$file = @fopen($logPath, "a+");
		static::$logPath = (!$file) ? false : $file;
		
		return static::$registered;
	}
	
	public static function exceptionHandler($exception) {

		$logMessage = "[".date(DATE_RFC822)."]";
		$logMessage .= " ".get_class($exception)." ";
		$logMessage .= ":";
		$logMessage .= $exception->getMessage();
		$logMessage .= PHP_EOL;
	
		if (static::$logPath != false) {
			fwrite(static::$logPath, $logMessage);
			fclose(static::$logPath);
		}
	
		exit($exception->getMessage() . PHP_EOL);
	}

}