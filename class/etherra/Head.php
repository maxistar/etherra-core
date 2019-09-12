<?php
namespace etherra;
/**
 * Returns head section for page
 * 
 * the order if output is following:
 * $head0
 * $styles
 * $head1* 
 * $scripts
 * $head2
 * 
 * @author maxim
 *
 */
class Head {
    protected $_scripts;
    protected $_styles;
    protected $_headers;
    /**
     * Constructor
     */
    function __construct(){
        $this->_scripts = new site_helper_SimpleScripter();
        $this->_styles = new site_helper_SimpleStyler();
        $this->_headers = new site_helper_Header();
    }
    /**
     * Adds script to head 
     */
	function addScript($name){
	    $this->_scripts->add($name);
	}
	/**
	 * Adds conditional script to head
	 */
	function addScriptCond($name,$condition){
	    $this->_scripts->add($name, $condition);
	}
	
	function addCss($name){
	    $this->_styles->add($name);
	}
	
	function addCssCond($name,$condition){
	    $this->_styles->add($name, $condition);
	}
	
	function addHead($value,$priority=0){
	    $this->_headers->add($value, $priority);
	}
	
	function getHtml(){
	    return $this->_headers->getHtml(0)
	           .$this->_styles->getHtml()
	           .$this->_headers->getHtml(1)
	           .$this->_scripts->getHtml()
	           .$this->_headers->getHtml(2);
	}
	
	
}