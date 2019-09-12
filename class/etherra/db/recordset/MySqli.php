<?php
namespace etherra;

class db_recordset_MySqli extends db_recordset_RecordSet {
    private $pos = 0;

    public function __construct($resource){
        $this->resource = $resource;
        $this->total = mysqli_num_rows($this->resource);
        $this->rewind();
    }

    function __destruct(){
        mysqli_free_result($this->resource);
    }

    public function rewind() {
        $this->pos = 0;
        $this->eof = $this->total <= $this->pos;
        if ($this->eof) return;
        mysqli_data_seek($this->resource,0);
        $this->fields = mysqli_fetch_array($this->resource,db_MySqli::$default_fetch_type);
        mysqli_data_seek($this->resource,0);
    }

    public function current() {
        $this->fields = mysqli_fetch_array($this->resource);
        mysqli_data_seek($this->resource,$this->pos);
        return $this->fields;
    }

    public function key() {
        return $this->pos;
    }

    public function next() {
        $this->pos++;
        $this->eof = $this->total<=$this->pos;
        return ($this->fields = mysqli_fetch_array($this->resource,db_MySqli::$default_fetch_type));
    }

    public function fetch($fetchtype=false) {
        $this->pos++;
        $this->eof = $this->total<=$this->pos;
        return ($this->fields = mysqli_fetch_array($this->resource, $fetchtype ? $fetchtype : db_MySqli::$default_fetch_type));
    }

    public function valid() {
        return !$this->eof;
    }

}
