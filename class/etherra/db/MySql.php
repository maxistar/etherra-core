<?php
namespace etherra;
/**
 *
 * MySQL driver for db_Db
 * @author maxim
 * @module db_Db
 */
class db_MySql extends db_Db {
	private $link;

	const FETCH_NAMES 	= 1;
	const FETCH_NUMS 	= 2;
	const FETCH_BOTH 	= 3; //

	static $default_fetch_type = db_MySql::FETCH_BOTH;

	function __construct($settings){
		$this->link = mysql_connect(
		$settings['host'],
		$settings['user'],
		$settings['password']
		);

		if (mysql_select_db($settings['name'],$this->link)===false){
			trigger_error(l('Can not connect to database!'));
		}


		if (isset($settings['encoding'])){
			$encoding = $settings['encoding'];
			$this->query("SET NAMES ".$encoding);
			$this->query("SET CHARACTER SET ".$encoding);
			$this->query("SET NAMES ".$encoding);
			$this->query("SET CHARACTER SET ".$encoding);
			$this->query("SET character_set_client='".$encoding."'");
			$this->query("SET character_set_connection='".$encoding."'");
			$this->query("SET character_set_database='".$encoding."'");
			$this->query("SET character_set_results='".$encoding."'");
			$this->query("SET character_set_server='".$encoding."'");
		}

	}
	/**
	 *
	 * @param $s
	 * @return unknown_type
	 */
	public function escapeString($s){
		return mysql_real_escape_string($s, $this->link);
	}

	public function query($query){
		$this->last_query = $query;
		$res = mysql_query($query);
		if ($res===TRUE){
			return TRUE; //no recordset needed there e.g. INSERT,UPDATE, etc. query type
		}
		elseif ($res===FALSE){
			trigger_error('DB Error '.mysql_errno($this->link).': '.$query.': '.mysql_error($this->link));
			return new db_recordset_RecordSet(null);
		}
		else {
			return new db_recordset_MySql($res);
		}
	}

	function native_query($query){
		$res = mysql_query($query);
		if ($res===FALSE) {
			trigger_error('DB Error '.mysql_errno($this->link).': '.$query.': '.mysql_error($this->link));
			return FALSE; //no recordset needed there e.g. INSERT,UPDATE, etc. query type
		}
		else
		return $res;

	}

	function getLastID(){
		return mysql_insert_id();
	}
}

