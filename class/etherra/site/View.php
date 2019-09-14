<?php
namespace etherra;

/**
 * very simple view class to show content located in folder inc/tpl
 * 
 * 
 * @author maxim
 *
 */
class site_View
{
    protected $_vars = array();
    protected $_suffix = '.tpl.php';
    protected $_paths = array();

    function __construct(){
        //look for theme template
        $this->addPath(_APP_ROOT . 'theme/' . conf('site.theme') . '/tpl/');
        //look for global template
        $this->addPath(_APP_ROOT . 'tpl/');

        $dir = _APP_ROOT . 'vendor/etherra';
        if (is_dir($dir)){
            if ($dh = opendir($dir)) {
                $files = array();
                while (($file = readdir($dh)) !== false) {
                    if (substr($file,0,1)=='.') continue;
                    if (!is_dir($dir.'/'.$file.'/tpl/')) continue;
                    $this->addPath($dir.'/'.$file.'/tpl/');
                }
                closedir($dh);
            }
        }

    }
    
    function addPath($path){
        $this->_paths[] = $path;
    }
    
    function display($_template, $_variables = array())
    {
        extract($this->_vars);
        extract($_variables);
 
        foreach($this->_paths as $path){
            if (file_exists($file = $path.$_template.$this->_suffix)) {
                include($file);
                return;
            };
        }
        print 'template "'.$_template.'" not found!'.print_r($this->_paths,true); //to raise erro?
    }
    
    function fetch($viewName, $variables = array())
    {
        ob_start();
        $this->display($viewName, $variables);
        $c = ob_get_contents();
        ob_end_clean();
        return $c;
    }

    function assign_by_ref($var_name, &$var_value){
        $this->_vars[$var_name] = $var_value;
    }
    
    
    public function assign($varName, $varValue = '')
    {
        if (is_array($varName)) {
            $this->_vars = array_merge($this->_vars, $varName);
        } else {
            $this->_vars[$varName] = $varValue;
        }
    }
    
    function cycle($values,$cyclename='default'){
        $c = View_Cycle::get($values, $cyclename);
        return $c->getValue();
    }
    
    function conf($name,$default=null){
        return Config::get($name,$default);
    }
    
    function h($value){
        return htmlspecialchars($value);
    }
    
    function l($value){
        return Lang::translate($value);
    }
    

}

class View_Cycle {
    var $index = 0;
    var $values = array();

    function __construct($values){
        $this->values = $values;
    }

    function getValue(){
        if ($this->index==count($this->values)){
            $this->index = 0;
        }
        return $this->values[$this->index++];
    }

    static function get($values,$name){
        static $instances = array();
        if (!isset($instances[$name])){
            $instances[$name] = new View_Cycle($values);
        }
        return $instances[$name];
    }

}
