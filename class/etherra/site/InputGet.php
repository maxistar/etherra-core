<?php
namespace etherra;

class site_InputGet extends site_Input {
	protected static $data;
	
	static function setRaw(&$data){
		self::$data = &$data;
	}
	
	static function getRaw(){
		return self::$data;
	}
	
	static function isNumber($name){
		return isset(self::$data[$name]) && is_numeric(self::$data[$name]);
	}
	 
	static function isDefined($name){
		return isset(self::$data[$name]);
	}
	 
	static function defined($name){
		return isset(self::$data[$name]);
	}
	
	static function get($name,$default=null){
		if (!isset(self::$data[$name])) return $default;
		return self::$data[$name];
	}
	 
	static function getNumber($name,$default=0){
		if (!isset(self::$data[$name])) return $default;
		return (int)self::$data[$name];
	}
	
	static function getInt($name,$default=0){
		if (!isset(self::$data[$name])) return $default;
		return (int)self::$data[$name];
	}
	 
	static function getString($name,$default=''){
		if (!isset(self::$data[$name])) return $default;
		return self::$data[$name];
	}
	 
	static function getArray($name,$default=array()){
		if (!isset(self::$data[$name]) || !is_array(self::$data[$name])) return $default;
		return self::$data[$name];
	}	
	
}

site_InputGet::setRaw($_GET);