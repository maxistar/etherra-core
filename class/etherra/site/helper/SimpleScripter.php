<?php
namespace etherra;

class site_helper_SimpleScripter extends site_helper_Helper {
    protected function getScriptUrl($script){
        if (substr($script,0,1)=='/'){
            if (file_exists($file = _SITE_ROOT.substr($script,1))){
                $time = filemtime($file);
                return $script.'?'.$time;
            }
            else {
                return '';
            }
        }
        
        $filename = $script.'.js';
        if (file_exists($file = _SITE_ROOT.'js'.DIRECTORY_SEPARATOR.$filename)){
            $time = filemtime($file);
            return '/js/'.$filename.'?'.$time;
        }
        $_parts = explode('/',$script);
        $filename = array_pop($_parts).'.js';
        $_mod = implode('/',$_parts);
        if (file_exists($file = _SITE_ROOT.'assets/'.$_mod.DIRECTORY_SEPARATOR.$filename)){
            $time = filemtime($file);
            return '/assets/'.$_mod.'/'.$filename.'?'.$time;
        }

        return '';
    }

    function add($name, $cond = 'default'){
    	parent::add($name, $cond); 
    }
    
    function getHtml(){
        $s = '';
        foreach($this->_items as $cond=>$i){
            $s .= $this->getHtmlCond($this->_items[$cond],$cond);
        }
        return $s;
    }

    public function getHtmlCond($items, $cond){
        $nl = "\n";
        $s = '';
        if ($cond!='default'){
            $s .= $nl . '<!--[if ' . $cond . ']>';
        }
        foreach($items as $script){
            $url = $this->getScriptUrl($script);
            if ($url){
                $s .= $nl . '<script type="text/javascript" src="'.$url.'"></script>'."\n";
            }
            else {
                if (conf('site.show_errors')){
                    $s .= $nl . '<script type="text/javascript">alert(\'script '.$script.' not found!\')</script>';
                }
            }
        }
        if ($cond!='default'){
            $s .= $nl . '<![endif]-->';
        }
        return $s;
    }
}