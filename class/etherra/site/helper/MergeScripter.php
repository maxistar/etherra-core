<?php
namespace etherra;

class site_helper_MergeScripter extends site_helper_Scripter {
   
   function getFilename($script){
   	   $filename = _SITE_ROOT.'js/'.$script.'.js';
   	   if (file_exists($filename)){
           return $filename;
   	   }
   	   
   	   $_parts = explode('/',$script);
       if (count($_parts)>1){
           $_mod = array_shift($_parts);
           $filename = _SITE_ROOT.'inc/mod/'.$_mod.'/js/'.implode('/',$_parts).'.js';
           if (file_exists($filename)) {
           		return $filename;
           }
       }
       return '';
   }    
    
   function _getHtml(){
      $max_time = 0;
      $compacted_filename = '';
      foreach($this->items as $script){
         $filename = $this->getFilename($script);
         if (!empty($filename)){
            $compacted_filename .= $script.'_';
        	$time = filemtime($filename);
        	if ($max_time<$time) $max_time = $time;
         }
         else {
        	Logger::write('script not found:'.$script,'error');
        	if(conf("site.show_errors")){
        		print('<script>alert("script not found:'.$script.'");</script>');
        	}
         }
      }
      
      
      if (Config::getBool('site.external_ajax')){
    	 $compacted_filename .= Ajax::getFunctionsHash().'_';	
      }  
        	
      $compacted_filename .= $max_time;
      if (Config::getBool('site.md5_scripts')){
         $compacted_filename = '/inc/cache/js/'.md5($compacted_filename).'.js';
      }
      else {
    	 $compacted_filename = '/inc/cache/js/'.str_replace('/','_',$compacted_filename).'.js';
      }
    	
      if (!file_exists(_SITE_ROOT.$compacted_filename)){
    	 $this->_createCompactedScript(_SITE_ROOT.$compacted_filename);
      }
      echo '<script type="text/javascript" src="'.$compacted_filename.'"></script>';
   }
    
   function _createCompactedScript($filename_new){ 
      $script_new = '';
      Util::ensureFolderExists(_SITE_ROOT.'inc/cache/js/');
  	  foreach($this->items as $script){
         $filename = $this->getFilename($script);
         if (file_exists($filename)){
			$f = fopen($filename,'r');	
			$script_new .= fread($f,filesize($filename))."\n";
			fclose($f);
    	 } 	
    	
      }
      if (Config::getBool('site.external_ajax')){
    	 $script_new .= Ajax::getJS('?ajax','ajax')."\n";	
      }
  	  $f = fopen($filename_new,'w');
  	  if (Config::getBool('site.compact_stylesheets')){
  		 fwrite($f,$this->_compressJavascript($script_new));
  	  }
      else {
  		 fwrite($f,$script_new);
  	  }
  	  fclose($f);
   } 

   function _compressJavascript($sJS){
  	  include_once "jsmin/jsmin-1.1.0.php";
  	  return JSMin::minify($sJS); 
   }  
}

