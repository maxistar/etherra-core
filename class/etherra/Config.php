<?php
/**
 * Config
 */
namespace etherra;

/**
 * loads data by demand and saves it in $data array depending on its name
* @author maxim
*
*/
class Config
{
    private static $data = array();
    private static $files = array();
    public static $admin_email;
    public static function get($name, $default = null)
    {
        if (!isset(self::$data[$name])) {
            self::loadSectionFor($name);
            //if once it loaded
            //it still have no value defined - just add null value
            //to avoid loading it every time it calls
            if (!isset(self::$data[$name])) {
                self::$data[$name] = $default;
            }
        }
        return self::$data[$name];
    }


    /**
     * @param $name
     * @param bool $default
     *
     * @return bool
     */
    public static function getBool($name, $default = false)
    {
        return (bool) self::get($name, $default);
    }

    /**
     * @param $name
     * @param int $default
     *
     * @return int
     */
    public static function getInt($name, $default = 0)
    {
        return (int) self::get($name, $default);
    }

    /**
     * @param $name
     * @param array $default
     *
     * @return mixed
     */
    public static function getArray($name, $default = array())
    {
        return self::get($name, $default);
    }

    /**
     * @param $name
     *
     * @return void
     */
    public static function loadSectionFor($name)
    {
        $parts = explode('.', $name);
        array_pop($parts);
        $name = implode('/', $parts);
        self::load($name);
    }

    /**
     * Loads section - it looks
     * first loading section.conf.php
     * section folder
     * load all modules if was not loaded in default section
     *
     * @param unknown_type $name
     *
     * @return void
     */
    public static function load($name)
    {
        if (isset(self::$files[$name])) {
            return;
        }
        self::$files[$name] = true;
        $files = array();
        //loading custom configs
        self::loadConfigs(_CONFIG_ROOT, $name, $files);

        //get all configs in modules
        $dir = _APP_ROOT . 'vendor/etherra';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file, 0, 1) == '.') {
                        continue;
                    }
                    if (!is_dir($dir.'/'.$file.'/config/')) {
                        continue;
                    }
                    self::loadConfigs($dir . '/' . $file . '/config/', $name, $files);
                }
                closedir($dh);
            }
        }

        //sort loaded array
        ksort($files);
        //load given config files
        foreach ($files as $name => $file) {
            self::includeFile($file);
        }
    }

    /**
     *
     * @param unknown $basedir
     * @param unknown $name
     *
     * @return void
     */
    public static function loadConfigs($basedir, $name, &$files)
    {
        $file = $basedir . $name . '.conf.php';
        $dname = '000_default'; //default
        if (is_file($file)) {
            if (!isset($files[$dname])) {
                $files[$dname] = $file;
            }
        }
        //get all files in folder
        $dir = $basedir . $name;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file, 0, 1) == '.') {
                        continue;
                    }
                    if (!is_file($dir . '/' . $file)) {
                        continue;
                    }
                    if (isset($files[$file])) {
                        continue;
                    }
                    $files[$file] = $dir . '/' . $file;
                }
                closedir($dh);
            }
        }
    }

    /**
     * Used function to isolate visiblitiy for local variables defined in config files
     *
     * @param unknown_type $file
     *
     * @return void
     */
    public static function includeFile($file)
    {
        include($file);
    }

    /**
     * @param $name
     * @param $value
     *
     * @return void
     */
    public static function setValue($name, $value)
    {
        self::$data[$name] = $value;
    }

    /**
     * Assigns data if it was not assigned before
     *
     * @param array $data
     *
     * @return void
     */
    public static function set($data)
    {
        self::$data += $data;
    }

    /**
     * Working like set but adding new properties to arrays
     * it is usefull for menu items
     * assigns data keeps old data unchanged but replaces new data
     *
     * @param array $data
     *
     * @return void
     */
    public static function merge($data)
    {
        foreach ($data as $key => $value) {
            if (isset(self::$data[$key]) && is_array(self::$data[$key])) {
                self::deepCopyArray(self::$data[$key], $value);
            } elseif (!isset(self::$data[$key])) {
                self::$data[$key] = $value;
            }
        }
    }

    /**
     * Copies values from one array to another
     *
     * @param array $dst .
     * @param array $src .
     *
     * @return void
     */
    protected static function deepCopyArray(&$dst, $src)
    {
        foreach ($src as $key => $value) {
            if (isset($dst[$key]) && (is_array($dst[$key]))) {
                self::deepCopyArray($dst[$key], $value);
            } elseif (!isset($dst[$key])) {
                $dst[$key] = $value;
            }
        }
    }
}
