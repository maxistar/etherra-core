<?php
namespace etherra;
/**
 * Db
 * Enter description here ...
 * @author maxim
 * @module db_Db
 */
abstract class db_Db {
	protected $last_query; //last query
	static function getInstance($settings){
		$driver = 'etherra\\db_'.$settings['type'];
		return new $driver($settings);
	}


	/**
	 * returns associated array
	 * @param unknown_type $query
	 * @return unknown
	 */
	function selectAssoc($query){
		$args = func_get_args();
		$r = call_user_func_array(array(&$this,'execute'),$args);
		return $r->associate();
	}

	function selectScalar($query){
		$args = func_get_args();
		$r = call_user_func_array(array(&$this,'execute'),$args);
		return $r->scalar();
	}

	function execute($query){
		$params = func_get_args();
		$num_args = count($params);
		if ($num_args>1) {
			if (is_array($params[1])){
				$params = $params[1];
			}
			else {
				//shift first param
				array_shift($params);
			}
			$query = $this->replacePlaceholders($query, $params);
		}
		return $this->query($query);
	}
	
	/**
	 * Replace ? placeholders
	 * @param unknown $query
	 * @param unknown $params
	 * @return string
	 */
	function replacePlaceholders($query, $params) {
		$query_parts = explode('?',$query);
		$query = $query_parts[0];
        $i = 1;
		foreach($params as $parameter){
			if (is_int($parameter) || is_float($parameter)) { //sometimes float variables for varchar field make MySQL break down
                $query .= $this->escapeString($parameter).$query_parts[$i++];
            } elseif (is_null($parameter)){
				$query .= 'null'.$query_parts[$i++];
			}
			else {
				$query .= '"'.$this->escapeString($parameter).'"'.$query_parts[$i++];
			}
		}
		return $query;
	}

	abstract function escapeString($s);

	abstract function getLastID();

	abstract function query($query);

	abstract function native_query($query);

	function getLastQuery(){
		return $this->last_query;
	}

}