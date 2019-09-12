<?php
namespace etherra;
/**
 * base class for all models
 * @author maxim
 *
 */
class tools_Item {

    var $exists = false;
    var $fields = null;
    var $original_values = array();
    var $rules = array();
    var $primary_key = '';
    var $table_name = '';

    function load($id){
        $fields = array();
        foreach($this->rules as $key=>$value){
            if (is_int($key)){ //simple rule
                $fields[] = $value;
            }
            else {
                $fields[] = $value[0].' as \''.$key.'\'';
            }
        }
        $sql = 'SELECT '.implode(', ',$fields).' FROM '.$this->table_name.' WHERE '.$this->primary_key.'=?';
        $r = Db::execute($sql,$id);
        if (!$r->eof){
        $num = 0;
        $this->fields = $r->fields;
        foreach($this->rules as $key=>$value){
            if (is_int($key)){
                $field_name = $value;
            }
            else {
                $field_name = $key;
            }

            if (is_int($this->$field_name)){
                $this->$field_name = (int)$r->fields[$num++];
            }
            elseif (is_float($this->$field_name)){
                $this->$field_name = (int)$r->fields[$num++];
            }
            else {
                $this->$field_name = $r->fields[$num++];
            }
            $this->original_values[$field_name] = $this->$field_name;
        }
        $this->exists = true;
        }
    }

    function isChanged(){
        foreach($this->rules as $key=>$value){
            if (is_int($key)){
                $field_name = $value;
            }
            else {
                $field_name = $key;
            }
            if ($this->original_values[$field_name]!=$this->$field_name){
                return true;
            }
        }
        return false;
    }

    function updateFields(){
    	foreach($this->rules as $key=>$value){
    		if (is_int($key)){
    			$fields[] = $value.'=?';
    			$values[] = $this->$value;
    		}
    		else {
    			$fields[] = $key.'='.$value[1];
    			$values[] = $this->$key;
    		}
    	
    	}
    	$key_name = $this->primary_key;
    	$values[] = $this->$key_name;
    	Db::execute("UPDATE ".$this->table_name." SET ".implode(', ',$fields)." WHERE ".$this->primary_key."=?",$values);
    }
    
    function update(){
        $this->updateFields();
        $this->onUpdate();
        return true;
    }

    function onUpdate(){}

    function onCreate(){}

    function delete(){
        $key_name = $this->primary_key;
        $values[] = $this->$key_name;
        Db::execute("DELETE FROM ".$this->table_name." WHERE ".$this->primary_key."=?",$values);
        return true;
    }

    function createItem(){
    	foreach($this->rules as $key=>$value){
    		if (is_int($key)){
    			$fields[] = $value.'=?';
    			$values[] = $this->$value;
    		}
    		else {
    			$fields[] = $key.'='.$value[1];
    			$values[] = $this->$key;
    		}
    	
    	}
    	Db::execute("INSERT INTO ".$this->table_name." SET ".implode(', ',$fields),$values);
    	$this->exists = true;
    	$name = $this->primary_key;
    	$this->$name = Db::getLastID();
    }
    
    function create(){
        $this->createItem();
        $this->onCreate();
        return true;
    }

    function restoreStringsFromPost($names){
        foreach($names as $name){
            $this->$name = $_POST[$name];
        }
    }

    function restoreIntsFromPost($names){
        foreach($names as $name){
            $this->$name = (int)$_POST[$name];
        }
    }

    function assignIntVars($arr,$vars){
        foreach($vars as $key=>$value){
            $this->$value = (int)$arr[$value];
        }
    }

    function assignFloatVars($arr,$vars){
        foreach($vars as $key=>$value){
            $this->$value = (float)$arr[$value];
        }
    }

    function assignStringVars($arr,$vars){
        foreach($vars as $key=>$value){
            $this->$value = $arr[$value];
        }
    }

    function save(){
        if ($this->exists){
            return $this->update();
        }
        else {
            return $this->create();
        }
    }
}