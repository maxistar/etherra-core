<?php
namespace etherra;

/**
 * the way to manage css styles 
 */
class site_helper_Helper {   
    /**
     * two dimentional array [$section][$key];
     */
    protected $_items = array();
     
    function add($name,$section=0){
        if (!isset($this->_items[$section])) {
            $this->_items[$section] = array();
        }
        if (!in_array($name,$this->_items[$section])){
            $this->_items[$section][] = $name;
        }
    }
    
}
