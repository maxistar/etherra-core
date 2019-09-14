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
    }
    
}

