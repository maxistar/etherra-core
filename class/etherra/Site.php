<?php
namespace etherra;

class Site
{
    static function init()
    {
        self::initConstants();
        self::loadDefaults();

        self::fixSiteAddress();
        date_default_timezone_set(conf('site.timezone', 'EST'));
        if (ini_get('magic_quotes_gpc')) {
            $_GET         = self::stripslashes($_GET);
            $_POST         = self::stripslashes($_POST);
            $_COOKIE     = self::stripslashes($_COOKIE);
        }

        error_reporting(conf('site.error_reporting', -1));
        if (php_sapi_name() == 'cli'){
            set_error_handler(array('etherra\\Site', 'errorHandlerText'));
        }
        else {
            set_error_handler(array('etherra\\Site', 'errorHandlerHTML'));
        }

        Hooks::initHooks();
        Hooks::callHook('Site::init');
    }    

    static function initConstants(){
    	//moved to site.php
    }
    
    static function loadDefaults(){
    	$dir = _APP_ROOT . 'vendor/etherra';
    	if (is_dir($dir)){
    		if ($dh = opendir($dir)) {
    			$files = array();
    			while (($file = readdir($dh)) !== false) {
    				if (substr($file,0,1)=='.') continue;
    				if (!is_file($default_file = $dir.'/'.$file.'/default.php')) continue;
    				include $default_file;
    			}
    			closedir($dh);
    		}
    	}
    	
    }
    
    static function getLang()
    {
        return conf('site.lang');
    }

    static function fixSiteAddress()
    {
        $home = conf('site.home');
        if (empty($home)) return;
        if (isset($_SERVER['HTTP_HOST'])) {
            if (('http://' . $_SERVER['HTTP_HOST']!=$home)) {
                header('Location: ' . $home.$_SERVER['REQUEST_URI']);
                die();
            }
        }
    }

    static function slashify($var)
    {
        $lastdig = substr($var, strlen($var)-1, 1);
        if (($lastdig!='/') or ($lastdig!='\\')) {
            return $var.'/';
        } else {
            return $var;
        }
    }


    static function stripslashes($val)
    {
        $type = gettype($val);
        if ($type == 'string') {
            return stripslashes($val);
        } elseif ($type == 'array') {
            $var1 = array();
            foreach ($val as $key=>$value) {
                $var1[$key] = self::stripslashes($val[$key]);
            }
            return $var1;
        } else {
            return $val;
        }
    }

    static function errorHandlerHTML($errno, $errstr, $errfile, $errline)
    {
        
        if ((error_reporting() & $errno ) == $errno) {
            $errfile = str_replace(getcwd(), '', $errfile);

            $string = "<strong>Warning </strong>: $errstr in <strong>$errfile</strong> on line 
            <strong>$errline</strong>";
            if (conf('site.show_errors', 1)) {
                print self::showStack($string);
            }
            self::log($errstr." in ".$errfile." on line ".$errline, 'error');
        }
    }
    
    static function errorHandlerText($errno, $errstr, $errfile, $errline)
    {
    
        if ((error_reporting() & $errno ) == $errno) {
            $errfile = str_replace(getcwd(), '', $errfile);
    
            if (conf('site.show_errors', 1)) {
                $string = "Warning : $errstr in $errfile on line $errline";
                print self::showStackText($string);
            }
            self::log($errstr." in ".$errfile." on line ".$errline, 'error');
        }
    }

    static function log($message, $name)
    {
        Logger::log($message, $name);
    }

    static function showStackText($sError, $exit = false)
    {
        echo $sError."\n";
        $aCallstack=debug_backtrace();
        foreach ($aCallstack as $aCall) {
            if ($aCall["function"] == 'showStack') continue;
            if ($aCall["function"] == 'errorHandler') continue;
            if (!isset($aCall['file'])) $aCall['file'] = '[PHP Kernel]';
            if (!isset($aCall['line'])) $aCall['line'] = '';
                echo "{$aCall["file"]}\t{$aCall["line"]}\t{$aCall["function"]}\n";
            }
            if ( $exit ) exit;
    }
    
    
    static function showStack($sError, $exit = false)
    {
        echo '<div style="text-align:center;"><table style="border:1px #000 solid;">
            <tr><td colspan="3" style="">'.$sError.'</td></tr>'."\n";
        $aCallstack=debug_backtrace();
        echo '<tr><th style="">file</th><th>line</th><th>function</th></tr>'."\n";
        foreach ($aCallstack as $aCall) {
            if ($aCall["function"] == 'showStack') continue;
            if ($aCall["function"] == 'errorHandler') continue;

            if (!isset($aCall['file'])) $aCall['file'] = '[PHP Kernel]';
            if (!isset($aCall['line'])) $aCall['line'] = '';
            echo "<tr><td style=\"font-size:8pt;\">{$aCall["file"]}</td><td style=\"font-size:8pt;\">
                    {$aCall["line"]}</td>".
                    "<td style=\"font-size:8pt;\">{$aCall["function"]}</td></tr>\n";
        }
        echo "</table></div>";
        if ( $exit ) exit;
    }
}

function conf($name,$default=null)
{
    return Config::get($name, $default);
}

function l($word)
{
    return Lang::translate($word);
}

function ll($word,$count)
{
    return Lang::plural($word, $count);
}

function h($str)
{
    return htmlspecialchars($str);
}