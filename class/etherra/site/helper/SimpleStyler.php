<?php
namespace etherra;

class site_helper_SimpleStyler extends site_helper_Helper{
    
    
    function getStyleUrl($style){
        if (substr($style,0,1)=='/'){
            if (file_exists($file = _SITE_ROOT.substr($style,1))){
                $time = filemtime($file);
                return $style.'?'.$time;
            }
            else {
                return '';
            }
        }
        
        $filename = $style.'.css';
        if (file_exists($file = _SITE_ROOT.'css/'.$filename)){
            $time = filemtime($file);
            return '/css/'.$filename.'?'.$time;
        }
        $_parts = explode('/',$style);
        $filename = array_pop($_parts).'.css';
        $_mod = implode('/',$_parts);
        
        if (file_exists($file = _SITE_ROOT.'assets/'.$_mod.'/'.$filename)){
            $time = filemtime($file);
            return '/assets/'.$_mod.'/'.$filename.'?'.$time;
        }
        return '';
    }

    function add($name, $cond = 'default'){
        parent::add($name, $cond); 
    }

    protected function getHtmlCond($items, $cond){
        $nl = "\n";
        $s = '';
        if ($cond!='default'){
            $s .= $nl . '<!--[if ' . $cond . ']>';
        }
        foreach($items as $stylesheet){
            $url = $this->getStyleUrl($stylesheet);
            if ($url){
                $s .= $nl . '<link rel="stylesheet" href="'.$url.'" type="text/css" />'."\n";
            }
            else {
                if (conf('site.show_errors')){
                    $s .= $nl . '<script type="text/javascript">alert(\'stylesheet '.$stylesheet.' not found!\')</script>';
                }
            }
        }
        if ($cond!='default'){
            $s .= $nl . '<![endif]-->';
        }
        return $s;
    } 
    
    function getHtml(){
        $s = '';
        foreach($this->_items as $cond=>$i){
            $s .= $this->getHtmlCond($this->_items[$cond], $cond);
        }
        return $s;
    }
    
    static function showHtml(){
    	print self::getHtml();
    }
    
}

