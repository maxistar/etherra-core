<?php
namespace etherra;
/**
 * tools_Ajax
 * 
 * @author maxistar
 */
class tools_Ajax {
	
	var $registered = array();
	var $forms = array();
	var $response = array();
	
	function process(){
		$r = $_POST['r']; 
		$request = json_decode($r,true);
		if (isset($request['f'])){
			$func_name = $request['f'];
			if (isset($this->registered[$func_name])){
				call_user_func_array($this->registered[$func_name],$request['a']);
				print json_encode($this->response);
			}
			else {
				$this->addAlert('method "'.$func_name.'" is not registered');
			}
		}
		elseif (isset($request['o'])){
			$form_name = $request['o'];
			if (isset($this->forms[$form_name])){
				call_user_func($this->forms[$form_name]);
				print "<html><body><script type='text/javascript'>window.onload=function(){window.parent.z.Ajax.renderResponses(";
				print json_encode($this->response);
				print ")}</script></body></html>";				
			}
			else {
				$this->addAlert('form "'.$form_name.'" is not registered');
			}
		}
	}

	function getFunctionsHash(){
		$s = "";
		foreach($this->registered as $function=>$method){
			$s .= $function;	
		}
		foreach($this->forms as $form=>$method){
			$s .= $form;	
		}
		return $s;		
	}
	
	
	function getJS($server_url){
		$s = "		
		
		z.ajax = new z.Ajax();
		
		z.ajax.server = '".$server_url."'\n";
		if (Config::get('site.show_errors')){
		    $s .= "z.ajax.debug = true;\n";
		}
		foreach($this->registered as $function=>$method){
			$s .= "function ".$function."(){z.ajax.callFunctionArg('".$function."',arguments)}\n";	
		}
		
		foreach($this->forms as $form=>$method){
			$s .= "z.ajax.initForm('".$form."');\n";
		}
		
		return $s;				
	}
	
	function getJavascript($server_url){
		$s = "<script type=\"text/javascript\">\n//<!-- \n";
		$s .= $this->getJS($server_url);
		$s .= '//-->
		</script>';
		return $s;
	}

	/**
	 * @param unknown_type $obj
	 * @param unknown_type $prefix
	 */
	function registerObject(&$obj,$prefix='ajax',$suffix=''){
		$methods = get_class_methods($obj);
		$prefix_len = strlen($prefix);
		foreach ($methods as $method_name){
			if (substr($method_name,0,$prefix_len)==$prefix){
				$this->registerFunction(array(&$obj, $method_name),$method_name.$suffix);
			}
		}
	}	
	
	function registerFunction($method,$function=false){
		if ($function!==false){
			$this->registered[$function] = $method;
		}
		elseif (is_string($method)){
			$this->registered[$method] = $method;
		}
		elseif (is_array($method) && isset($method[1])){
			$this->registered[$method[1]] = $method;
		}
	}
	
	function registerForm($method, $form_id){
		$this->forms[$form_id] = $method;
	}
	
	function addAlert($message){
		$this->response[] = array('c'=>'a','m' => $message);
	}

	function addAssign($element_id,$property,$value){
		$this->response[] = array('c'=>'s','e'=>$element_id,'p'=>$property,'v'=>$value);
	}
	
	function addScriptCall($args){
		$arguments = array();
		for($i=1;$i<count($args);$i++){
			$arguments[] = $args[$i];
		}
		$this->response[] = array('c'=>"sc",'f'=>$args[0],'a'=>$arguments);
	}	
	
	function addScript($script){
		$this->response[] = array('c'=>'e','s'=>$script);		
	}
}




