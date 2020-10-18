<?php
namespace etherra;

class Hooks {

    static $hooks = array();

    static function registerHook($name,$function) {
        if (!isset(self::$hooks[$name])) {
            self::$hooks[$name]=array($function);
        }
        else {
            self::$hooks[$name][] = $function;
        }
    }

    static function callHook($name) {
        $arguments = func_get_args();
        array_shift($arguments);

        if (isset(self::$hooks[$name])) {
            foreach(self::$hooks[$name] as $hook) {
                call_user_func_array($hook,$arguments);
            }
        }
    }

    static function initHooks() {
        if (is_file($file = _CONFIG_ROOT . 'hooks.conf.php')) {
            include $file;
        }

        //call folder based hooks
        $dir = _CONFIG_ROOT . 'hooks/';
        $files = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file,0,1)!=='.'){
                        $files[] = $file;
                    }
                }
                closedir($dh);
            }
        }
        sort($files);
        foreach($files as $file) {
            include($dir . $file);
        }

        self::callHook('Hooks::initHooks');
        self::callHook('Hooks::init');
    }
}
