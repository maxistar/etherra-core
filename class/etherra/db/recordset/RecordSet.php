<?php
namespace etherra;

class db_recordset_RecordSet implements \Iterator {
    public $eof = true;
    public $fields = array();
    public $total = 0;
    public $resource;

    public function associate(){
        $initial=array();
        while ($row = $this->next()){
            $initial[$row[0]] = $row[1];
        }
        return $initial;
    }

    public function __get($name){
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }
    }

    public function scalar(){
        if ($this->eof){
            return null;
        }
        return $this->fields[0];
    }

    public function getTotal(){
        return $this->total;
    }

    public function getArray($fetch_type=false){
        $data = array();
        while($row = $this->fetch($fetch_type)){
            $data[] = $row;
        }
        return $data;
    }

    public function results($fetch_type=false){
        return $this->getArray($fetch_type);
    }

    public function rewind() {
    }
    public function current() {
    }
    public function key() {
    }
    public function next() {
        return FALSE;
    }
    /**
     * same as next but with selecting of fetch type
     */
    public function fetch($type=false) {
        return FALSE;
    }
    public function valid() {
        return FALSE;
    }

    public function getResource(){
        return $this->resource;
    }

}
