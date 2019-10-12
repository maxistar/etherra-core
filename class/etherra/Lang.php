<?php
namespace etherra;

class Lang {
    static $data = array();
    static $files = array();

    static $plural_count = 2;
    static $plural_func;
    static $TrasliteReplaces = array();

    /**
     * translate the string
     * this can be single string or string with parameters
     * @param unknown_type $word
     */
    static function translate($word) {
        if (isset(self::$data[$word]))
             $word = self::$data[$word];
        if (func_num_args() ==1 ) return $word;

        $args = func_get_args();
        array_shift($args);
        array_unshift($args, $word);
        return call_user_func_array('sprintf', $args);
    }

    static function load($name) {
        if (!isset(self::$files[$name])) {
            self::$files[$name] = true;
            $suffix = (conf('site.charset') == '' || conf('site.charset') == 'utf-8') ? '' : '_' . conf('site.charset');
            
            $dir = _APP_ROOT . 'vendor/etherra';
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if (substr($file, 0, 1) == '.') {
                            continue;
                        }
                        if (!is_dir($dir . '/' . $file . '/lang/')) {
                            continue;
                        }
                        self::loadFile($dir . '/' . $file . '/lang/', $name, $suffix);
                    }
                    closedir($dh);
                }
            }
            self::loadFile(_APP_ROOT . 'theme/' . conf('site.theme') . '/lang/', $name, $suffix);
            self::loadFile(_APP_ROOT . 'lang/', $name, $suffix);
        }
    }
    
    static function loadFile($basedir, $name, $suffix){
        $filename = $basedir . $name . '.' . conf('site.lang') . $suffix . '.php';

        if (file_exists($filename)) {
            include($filename);
        }

        //load folders
        $dir = $basedir . $name . '_' . conf('site.lang') . $suffix;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file, 0, 1) == '.') {
                        continue;
                    }
                    if (!is_file($dir . '/' . $file)) {
                        continue;
                    }
                    include($dir . '/' . $file);
                }
                closedir($dh);
            }
        }

    }

    static function current() {
        return conf('site.lang');
    }

    static function plural($word, $count) {
        $form = call_user_func(self::$plural_func, $count);
        if (isset(self::$data[$word][$form])) {
            return sprintf(self::$data[$word][$form], $count);
        }
        return sprintf($word, $count);
    }

    static function translite($string) {
        $string = strtolower($string, "UTF8");

        foreach(Lang::$TrasliteReplaces as $key => $value) {
            $string = str_replace($key, $value, $string);
        }
        if(preg_match_all('/[a-zA-Z0-9\_\.]/', $string, $matches)) {
            $string = join("", $matches[0]);
        }
       return $string;
    }

    static function set($data) {
        self::$data += $data;
    }
}
