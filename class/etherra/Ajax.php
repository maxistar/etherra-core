<?php
namespace etherra;
/**
 * static interface for tools_Ajax
 * 
 * @author maxistar
 */
class Ajax {

    static function &instance(){
    	static $ajax;
    	if (!isset($ajax)){
    		 $ajax = new tools_Ajax();
    	}
    	return $ajax;
    }
    
    static function registerFunction($method,$function=false){		
		$ajax = Ajax::instance();
		if (is_array($function)){ //compatibility hack - swap arguments
			$ajax->registerFunction($function,'ajax'.$method);
			return;
		}
		$ajax->registerFunction($method,$function);
	}
	
	/**
	 * registers all methods strted from prefix for ajax calls
	 * @param unknown_type $obj
	 * @param unknown_type $prefix
	 */
	static function registerObject(&$obj,$prefix='ajax',$suffix=''){
		$ajax = Ajax::instance();
		$ajax->registerObject($obj,$prefix,$suffix);
	}
	
	/**
	 * for compatibility
	 * #deprecated
	 */
	static function registerFunctions(&$obj,$prefix='ajax',$suffix=''){
		self::registerObject($obj,$prefix,$suffix);
	}

	/**
	 * for compatibility
	 * #deprecated
	 */	
	static function registerAjaxFunctions(&$obj,$prefix='ajax',$suffix=''){
		self::registerObject($obj,$prefix,$suffix);
	}
	
	static function registerForm($function, $form_id){
		$ajax = Ajax::instance();
		if (is_array($form_id)){
			//compatibility hack - swap arguments
			$ajax->registerForm($form_id,$function);
			return;
		}
		$ajax->registerForm($function, $form_id);
	}

	static function addAlert($msg){
		$ajax = Ajax::instance();
		$ajax->addAlert($msg);	
	}	
	
	static function addScript($script){
		$ajax = Ajax::instance();
		$ajax->addScript($script);	
	}
	
	static function addAssign($element_id,$property,$value){
		$ajax = Ajax::instance();
		$ajax->addAssign($element_id,$property,$value);
	}

	static function addScriptCall(){
		$ajax = Ajax::instance();
		$ajax->addScriptCall(func_get_args());
	}
	
	static function addMethodCall(){
		$ajax = Ajax::instance();
		$ajax->addMethodCall(func_get_args());
	}
	
	/**
	 * returns concatenated string of registered finction names
	 * used for storing finctios in external file
	 */
	static function getFunctionsHash(){
		$ajax = Ajax::instance();
		return $ajax->getFunctionsHash();
	}
	
	static function process(){
		$ajax = Ajax::instance();
		$ajax->process();		
	}
	
	static function getJavascript($server_url){
		$ajax = Ajax::instance();
		return $ajax->getJavascript($server_url);
	}
	
	static function getJS($server_url){
		$ajax = Ajax::instance();
		return $ajax->getJS($server_url);
	}	
}