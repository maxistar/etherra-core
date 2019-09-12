<?php
namespace etherra;

class db_recordset_MySql extends db_recordset_RecordSet {
    private $pos = 0;

    public function __construct($resource){
        $this->resource = $resource;
        $this->total = mysql_num_rows($this->resource);
        $this->rewind();
    }

    function __destruct(){
        mysql_free_result($this->resource);
    }

    public function rewind() {
        $this->pos = 0;
        $this->eof = $this->total <= $this->pos;
        if ($this->eof) return;
        mysql_data_seek($this->resource,0);
        $this->fields = mysql_fetch_array($this->resource,db_MySql::$default_fetch_type);
        mysql_data_seek($this->resource,0);
    }

    public function current() {
        $this->fields = mysql_fetch_array($this->resource);
        mysql_data_seek($this->resource,$this->pos);
        return $this->fields;
    }

    public function key() {
        return $this->pos;
    }

    public function next() {
        $this->pos++;
        $this->eof = $this->total<=$this->pos;
        return ($this->fields = mysql_fetch_array($this->resource,db_MySql::$default_fetch_type));
    }

    public function fetch($fetchtype=false) {
        $this->pos++;
        $this->eof = $this->total<=$this->pos;
        return ($this->fields = mysql_fetch_array($this->resource, $fetchtype ? $fetchtype : db_MySql::$default_fetch_type));
    }

    public function valid() {
        return !$this->eof;
    }

}
