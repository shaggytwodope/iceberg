<?php

namespace iceberg;

class ErrorHandler {

	private static $registered = false;
	private static $logPath = false;

	public static function register($logPath = false) {
		if (!static::$registered)
			static::$registered = set_exception_handler(__CLASS__."::exceptionHandler");
	
		if ($logPath) {
			$file = @fopen($logPath, "a+");
			static::$logPath = (!$file) ? false : $file;
		}
		
		return static::$registered;
	}

	public static function log($name, $message, $log = false) {

		$logMessage = "[".date(DATE_RFC822)."]";
		$logMessage .= " ".$name." ";
		$logMessage .= ": ";
		$logMessage .= $message;
		$logMessage .= PHP_EOL;
		
		if (static::$logPath && $log) 
			fwrite(static::$logPath, $logMessage);
		
		echo "[!!] " . $message . PHP_EOL;
	}

	public static function exceptionHandler($exception) {
		static::log( get_class($exception), $exception->getMessage(), true );

		if (static::$logPath) 
			fclose(static::$logPath);

		exit(1);
	}

}