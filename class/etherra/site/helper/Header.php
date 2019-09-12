<?php
namespace etherra;

/**
 * the way to manage css styles 
 * static class
 */

class site_helper_Header extends site_helper_Helper {   
  
   /**
    * removes script by value or by key
    * @param variant $name
    */
   function remove($name, $priority=1){
      if (isset($this->_items[$priority])){
          if (($key=array_search($name,$this->_items[$priority]))!==FALSE){
	         unset($this->_items[$key]);
	      }
      }
   }   
   
   function getHTML($priority=0){
       if (!isset($this->_items[$priority])) return;
       $s = '';
      
       foreach($this->_items[$priority] as $item){
	       $s .= $item."\n";
       }
	   return $s;
   }
}
