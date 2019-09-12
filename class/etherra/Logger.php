<?php
namespace etherra;

class Logger {
	static function write($message, $name=''){
		$dir = _SITE_ROOT."inc/cache/logs/";
		self::ensureFolderExists($dir);
		$filename = $dir.$name.date("Ymd").".log";
		$f = fopen($filename,'a');
		$request_uri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'[not set]';
		$browser = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'unknown';
		fwrite($f,"[".date("H:m:s")."] ".$request_uri." ".$message." ".$browser."]\n");
		fclose($f);
	}

	static function log($message, $name){
		self::write($message, $name);
	}

	static function ensureFolderExists($path){
		if (!is_dir($path)){
			self::mkDirs($path);
		}
	}

	static function mkDirs($strPath){
		if (is_dir($strPath)) return true;
		$pStrPath = dirname($strPath);
		if (!self::mkDirs($pStrPath)) return false;
		return mkdir($strPath);
	}


}
