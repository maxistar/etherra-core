<?php
namespace etherra;
/**
 *
 * MySQL driver for db_Db
 * @author maxim
 * @module db_Db
 */
class db_MySqlNd extends db_Db {
	private $link;

	const FETCH_NAMES 	= MYSQLND_ASSOC;
	const FETCH_NUMS 	= MYSQLND_NUM;
	const FETCH_BOTH 	= MYSQLND_BOTH; //

	static $default_fetch_type = self::FETCH_BOTH;

	function __construct($settings){
		$this->link = new \mysqli(
		$settings['host'],
		$settings['user'],
		$settings['password']
		);

		if ($this->link->select_db($settings['name'])===false){
			trigger_error(l('Can not connect to database!'));
		}
	}
	/**
	 *
	 * @param $s
	 * @return unknown_type
	 */
	public function escapeString($s){
		return $this->link->real_escape_string($s);
	}

	public function query($query){
		$this->last_query = $query;
        $stmt = $this->link->prepare($query);
        if ($stmt === false){
            trigger_error('DB Error '.$this->link->errno.': '.$query.': '.$this->link->error);
            return new db_recordset_RecordSet(null);
        }
        $res = $stmt->execute();
        if ($res === false){
            trigger_error('DB Error '.$this->link->errno.': '.$query.': '.$this->link->error);
            return new db_recordset_RecordSet(null);
        }

        $metadata = $stmt->result_metadata();
        if ($metadata === false) {
            return new db_recordset_RecordSet(null);
        }
        $metadata->close();

        $result = $stmt->get_result();
        if ($result === false) {
            trigger_error('DB Error '.$this->link->errno.': '.$query.': '.$this->link->error);
            return new db_recordset_RecordSet(null);
        }

        $stmt->close();
        if ($result) {
            return new db_recordset_MySqlNd($result);
        }

        trigger_error('DB Error '.$this->link->errno.': '.$query.': '.$this->link->error);
        return new db_recordset_RecordSet(null);
	}

	function native_query($query){
		$res = $this->link->query($query);
		if ($res===FALSE) {
            trigger_error('DB Error '.$this->link->errno.': '.$query.': '.$this->link->error);
			return FALSE; //no recordset needed there e.g. INSERT,UPDATE, etc. query type
		}
		else
		return $res;

	}

	function getLastID(){
		return $this->link->insert_id;
	}
}