<?php
/**
 * same Template class but with protected methods
 * @author maxim
 *
 */
namespace etherra;

class site_Url {
	static $url;
    static $url_parts;
    static $url_parts_count;
    static function init(){
        $url = $_SERVER['REQUEST_URI'];
        //cut off domain name and protocol prefix
        $home = conf('site.home');
        if(!empty($home)){
            if (strpos($url,$home)===0){
                $url = substr($url,strlen($home));
            }
        }
        self::$url = $url;
        //cut off question mark
        $r = strpos($url,'?');
        if ($r!==FALSE){
            $url = substr($url,0,$r);
        }
		$url = trim($url,'/');
		if (empty($url)) {
			self::$url_parts = array();
		}
		else {
        	self::$url_parts = explode('/',$url);
		}
        self::$url_parts_count = count(self::$url_parts);    	
    }
}

site_Url::init();