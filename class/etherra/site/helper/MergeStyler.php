<?php
namespace etherra;

class site_helper_MergeStyler extends site_helper_Styler {
    
   function getFilename($style){
       //return _CSS_ROOT.$style.'.css';
       $filename = _SITE_ROOT.'css/'.$style.'.css';
   	   if (file_exists($filename)){
           return $filename;
   	   }
   	   
   	   $filename = _SITE_ROOT.'inc/theme/'.conf('site.theme').'/css/'.$style.'.css';
   	   if (file_exists($filename)){
   	   	return $filename;
   	   }   	   
   	   
   	   $_parts = explode('/',$style);
       if (count($_parts)>1){
           $_mod = array_shift($_parts);
           $filename = _SITE_ROOT.'inc/mod/'.$_mod.'/css/'.implode('/',$_parts).'.css';
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
				Logger::write('script not found:'.$script);
				if(conf("site.show_errors")){
					print('<script>alert("style not found:'.$style.'");</script>');
				}
        	}
    	}
    	$compacted_filename .= $max_time;
    	
    	if (Config::getBool('site.md5_stylesheets')){
    		$compacted_filename = '/inc/cache/css/'.md5($compacted_filename).'.css';
    	}
    	else {
    		$compacted_filename = '/inc/cache/css/'.str_replace('/','_',$compacted_filename).'.css';
    	}

    	if (!file_exists(_SITE_ROOT.$compacted_filename)){
    		$this->_createCompactedStylesheet(_SITE_ROOT.$compacted_filename);
    	}
    	return '<link rel="stylesheet" href="'.$compacted_filename.'" type="text/css" />';
   }
   
   function _createCompactedStylesheet($filename_new){
  	  $script_new = '';
  	  Util::ensureFolderExists(_SITE_ROOT.'inc/cache/css/');
  	  foreach($this->items as $script){
         $filename = $this->getFilename($script);
         if (file_exists($filename)){
			$f = fopen($filename,'r');	
			$script_new .= fread($f,filesize($filename));
			fclose($f);
    	 }
      }
      $f = fopen($filename_new,'w');
      if (Config::getBool('site.compact_stylesheets')){
  		 fwrite($f,$this->_CompressCSS($script_new));
      }
      else {
  		 fwrite($f,$script_new);
      }
      fclose($f);
   } 

   function _compressCSS($sJS){
  	  include_once "cssmin/cssmin.php";
  	  return CSSMin::minify($sJS); 
   }    
}

