<?php

class site_PhpRpc {
	static $timeout = 20;
	
	/**
	 * encodes functions name and arguments and posts these to remote server
	 * Enter description here ...
	 * @param unknown_type $host
	 * @param unknown_type $script
	 * @param unknown_type $function_name
	 * @param unknown_type $args
	 */
	static function call($host,$script,$function_name,$args){
		$request = serialize(array($function_name,$args));
  		$response = self::postRequest($request,$host,$script);
  		$res = unserialize(urldecode($response));
  		if ($res===false){
  			trigger_error($response);
  		}
  		return $res;
	}
	
	static function encodeResponse($data){
		print urlencode(serialize($data));
	}
	
	/**
	 * returns array($function_name,$args)
	 * Enter description here ...
	 * @return mixed
	 */
  	static function getRequest(){
    	$xmldata = file_get_contents("php://input");
    	return unserialize(urldecode($xmldata));  		
  	}	
	
	static function postRequest($string,$host,$script){ 
  		$raw_response = self::sendPostData(urlencode($string),$script,$host,80);
  		$raw_response = str_replace("\r\n","\n",$raw_response);
  		$response = substr($raw_response,strpos($raw_response,"\n\n")+2);
  		return $response;
  	}
  	
  	
  
  	static function sendPostData($post_data,$request_url,$host,$port) {
  		$str  = "POST http://".$host.$request_url." HTTP/1.0\n";
    	$str .= "Accept: */*\n";
    	$str .= "Host: ".$host."\n";
    	$str .= "User-Agent: XMLRPC\n";
    	$str .= "Connection: Close\n";
    	$str .= "Cache-Control: no-cache\n";
    	$str .= "Content-Type: application/x-www-form-urlencoded\n";
    	$str .= "Content-Length: ".strlen($post_data)."\n\n";
		$str .= $post_data;

		$errstr   = "Unknown error";
		$errno    = "-1";
		
		set_time_limit(9999);
		$fp = fsockopen($host, $port, $errno, $errstr, self::$timeout);
	    if ( $fp ) {
	       		fputs($fp, $str);
				$ret = '';
	        	while (!feof($fp)) {
					$ret .= fgets($fp, 1024);
				}
            	fclose($fp);
				return $ret;
		}
		else {
				$socket_error = "Socket error: ".$errstr." (".$errno.")";
				print $socket_error;
		}
		
		return "";
  	}
}