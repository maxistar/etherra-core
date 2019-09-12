<?php
namespace etherra;

/**
 * very simple page class to show content located in folder inc/tpl
 * it contains only head and view
 * 
 * @author maxim
 *
 */
class site_SimplePage
{
    protected $_view;
    public $pageTemplate = 'page/page';
    protected $_head;
    private $_headPlaceholder = '<!-- head space to be replaced last -->';
    public $title;
    protected $scripts = array();
    /**
     * sdditional scripts to be assigned to head
     * @var unknown_type
     */
    protected $styles = array();
    
    static $instance;

    public function __construct()
    {
    	self::$instance = $this;
        $this->_view = new site_View();
        $this->_view->assign('page',$this);
        
        $this->_head = new Head();
        $this->_view->assign('head', $this->_head);
     	$this->_head->addScript('etherra/core/ztools');   
    }
    
    function showHeadSpace()
    {
    	echo $this->_headPlaceholder;
    }
    
    /**
     * shows the page
     */
    
    
    function show()
    {
    	$this->_view->assign('title', $this->title);
    
    	Hooks::callHook('site_SimplePage::before_output');
    	$content = $this->_view->fetch($this->pageTemplate);
    	 
    	
    	//styles defined in main file have lowest priority
    	foreach($this->scripts as $name){
    		$this->_head->addScript($name);
    	}
    	foreach($this->styles as $name){
    		$this->_head->addCss($name);
    	}
    	print str_replace($this->_headPlaceholder, $this->_head->getHtml(), $content);
    }
    
    public function addScript($name){
    	$this->_head->addScript($name);
    }
    
    public function addCss($name){
    	$this->_head->addCss($name);
    }
    
    public function addStyle($name){
    	$this->_head->addCss($name);
    }
    
    public function addHead($value){
        $this->_head->addHead($value);
    }
    
    
    public function assign($name,$value=null){
    	$this->_view->assign($name,$value);
    }
    
    /**
     * 
     * @return \etherra\Head
     */
    public function getHead(){
    	return $this->_head;
    }

    /**
     * shows the section
     * 
     * @param unknown_type $name
     */
    public function showSpace($name)
    {
    	Hooks::callHook('site_SimplePage::showSpace', $name);
        Hooks::callHook('site_SimplePage::showSpace_'.$name.'_1');
        
        //if section function exists - call it!
        $functionName = 'show'.$name.'Space'; //e.g. showMainSpace
        if (method_exists($this, $functionName)) {
            call_user_func(array(&$this, $functionName));
        }
        
        Hooks::callHook('site_SimplePage::showSpace_'.$name.'_2');
    }
    
    public function display($name,$vars=array()){
    	$this->_view->display($name,$vars);
    }
    
    public function fetch($name,$vars=array()){
        return $this->_view->fetch($name,$vars);
    }
    
}
