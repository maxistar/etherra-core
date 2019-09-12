<?php
namespace etherra;

class db_recordset_MySqlNd extends db_recordset_RecordSet {
    private $pos = 0;

    private $_resultSet;

    public function __construct($resource){
        $this->resource = $resource;
        $this->resultSet = $resource;
        $this->total = $this->resultSet->num_rows;
        $this->rewind();
    }

    function __destruct(){
        $this->resultSet->close();
    }

    public function rewind() {
        $this->pos = 0;
        $this->eof = $this->total <= $this->pos;
        if ($this->eof) return;
        $this->resultSet->data_seek(0);
        $this->fields = $this->resultSet->fetch_array(db_MySqlNd::$default_fetch_type);
        $this->resultSet->data_seek(0);
    }

    public function current() {
        $this->fields = $this->resultSet->fetch_array();
        $this->resultSet->data_seek($this->pos);
        return $this->fields;
    }

    public function key() {
        return $this->pos;
    }

    public function next() {
        $this->pos++;
        $this->eof = $this->total<=$this->pos;
        return ($this->fields = $this->resultSet->fetch_array(db_MySqlNd::$default_fetch_type));
    }

    public function fetch($fetchtype=false) {
        $this->pos++;
        $this->eof = $this->total<=$this->pos;
        return ($this->fields = $this->resultSet->fetch_array($fetchtype ? $fetchtype : db_MySqlNd::$default_fetch_type));
    }

    public function valid() {
        return !$this->eof;
    }

}
