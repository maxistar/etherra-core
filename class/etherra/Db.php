<?php
namespace etherra;
/**
 * Static Interface for db_Db class
 * 
 * @author maxim
 * @module site
 */
abstract class Db {
	/**
	 * Returns instance of Db class
	 * 
	 * @return etherra\db_Db
	 */
	static function singleton()
	{
		static $db = null;
		if ($db==null){
			$settings = array(
				'type'=>conf('site.dbtype','MySql'),
				'host'=>conf('site.dbhost','localhost'),
				'user'=>conf('site.dbuser','root'),
				'name'=>conf('site.dbname','root'),
				'encoding'=>conf('site.dbencoding'),
				'password'=>conf('site.dbpassword'),
			);
			
			
			$db = db_Db::getInstance($settings); 
		}
		return $db;
	}
	
	/**
	 * Returns instance of database adapter
	 * 
	 * @return etherra\db_Db
	 */
	static function getInstance()
	{
		return self::singleton();
	}
	
	/**
	 * 
	 * @param unknown_type $query
	 */
	static function selectScalar($query){
		$db = self::singleton();
		$args = func_get_args();
		return call_user_func_array(array($db,'selectScalar'), $args);
	}
	
	/**
	 * returns associated array
	 *
	 * @param unknown_type $query
	 * @return unknown
	 */
	static function selectAssoc($query){
		$db = self::singleton();
		$args = func_get_args();
		return call_user_func_array(array($db,'selectAssoc'), $args);
	}	
	
	/**
	 * 
	 * executes query
	 * @param unknown_type $query
	 */
	static function execute($query){
		$db = self::singleton();
		$args = func_get_args();
		return call_user_func_array(array($db,'execute'), $args);
	}
	
	/**
	 * simple version without any processing of placeholders signs
	 *
	 * @param unknown_type $query
	 * @return unknown
	 */
	static function query($query){
		$db = self::singleton();		
		return $db->query($query);
	}
	
	static function mysql_query($query){
		$db = self::singleton();
		return $db->native_query($query);
	}
	
	static function native_query($query){
		$db = self::singleton();
		return $db->native_query($query);
	}
	
	/**
	 * escapes string according engine rules
	 * @param unknown_type $s
	 */
	static function escapeString($s){
		$db = self::singleton();
		return $db->escapeString($s); 
	}
	
	/**
	 * returns last autoincrement id
	 * 
	 */
	static function getLastID(){
		$db = self::singleton();		
		return $db->getLastID(); 
	}
	
	static function getLastQuery(){
		$db = self::singleton();		
		return $db->getLastQuery(); 
	}
	
	
}



