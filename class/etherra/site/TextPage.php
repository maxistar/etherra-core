<?php
namespace etherra;

/**
 * 
 * 
 * 
 * created for http://trainingforthetroops.com
 * @author maxim
 *
 */
class site_TextPage extends site_SimplePage
{
    protected $_template;
    
    function __construct($props)
    {
        parent::__construct();
        foreach (array('title', 'template', 'pageTemplate', 'scripts', 'styles') as $name) {
            if (isset($props[$name])) {
                $var = '_'.$name;
                $this->$var = $props[$name];
            }
        }
        
    }
    
    function showMainSpace()
    {
        $this->_view->display($this->_template);
    }
    
    

}
