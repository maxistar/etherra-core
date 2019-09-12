<?php
namespace etherra;

class Session {
	/**
	 * starts is not started
	 * Enter description here ...
	 */
	static $started = false;
    static function start(){
        if (!session_id()){
            session_start();
        }
        self::$started = true;
    }

    static function isStarted(){
        return self::$started;
    }
    
    /**
     * sometimes we need to close it before logn operation
     * see:http://maxistar.ru/blog/%D0%A1%D0%B5%D0%BC%D0%B5%D1%80%D0%BE-%D0%BE%D0%B4%D0%BD%D0%BE%D0%B3%D0%BE-%D0%B6%D0%B4%D1%83%D1%82-%D0%B8%D0%BB%D0%B8/
     */
    static function close(){
    	session_write_close();
    	self::$started = false;
    }
    
    /**
     * this function does not starts session automatically if sesion was not started before
     * if session cookie not created that means that variable is also not defined!!!
     * Enter description here ...
     * @param unknown_type $name
     */
    static function is_set($name){
        if (!isset($_COOKIE[session_name()]) && !self::$started) {
        	return false;	
        }
        self::start();
        return isset($_SESSION[$name]);
    }
    
    static function un_set($name){
        self::start();
        unset($_SESSION[$name]);
    }
    
    static function set($name,$value){
        self::start();
        $_SESSION[$name] = $value;
    }
    
    static function get($name,$default=null){
        self::start();
        if (isset($_SESSION[$name])) return $_SESSION[$name];
        return $default;
    }    
}
