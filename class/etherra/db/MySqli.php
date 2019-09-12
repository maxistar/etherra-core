<?php
namespace etherra;
/**
 *
 * MySQL driver for db_Db
 * @author maxim
 * @module db_Db
 */
class db_MySqli extends db_Db {
	private $link;

	const FETCH_NAMES 	= 1;
	const FETCH_NUMS 	= 2;
	const FETCH_BOTH 	= 3; //

	static $default_fetch_type = db_MySqli::FETCH_BOTH;

	function __construct($settings){
		$this->link = \mysqli_connect(
		    $settings['host'],
		    $settings['user'],
		    $settings['password'],
            $settings['name']
		);

		if (!$this->link){
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
		return mysqli_real_escape_string($this->link, $s);
	}

	public function query($query){
		$this->last_query = $query;
		$res = mysqli_query($this->link, $query);
		if ($res===TRUE){
			return TRUE; //no recordset needed there e.g. INSERT,UPDATE, etc. query type
		}
		elseif ($res===FALSE){
			trigger_error('DB Error '.mysqli_errno($this->link).': '.$query.': '.mysqli_error($this->link));
			return new db_recordset_RecordSet(null);
		}
		else {
			return new db_recordset_MySqli($res);
		}
	}

	function native_query($query){
		$res = mysqli_query($this->link, $query);
		if ($res===FALSE) {
			trigger_error('DB Error '.mysqli_errno($this->link).': '.$query.': '.mysqli_error($this->link));
			return FALSE; //no recordset needed there e.g. INSERT,UPDATE, etc. query type
		}
		else
		return $res;

	}

	function getLastID(){
		return mysqli_insert_id($this->link);
	}
}

