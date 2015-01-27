<?php
/**
 * 加载器
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

class Loader
{
    public static function AutoLoad($class)
    {
        # todo check path
        require(BASE_DIR . '/' . str_replace('\\', '/', $class) . '.php');
    }


    private static $_config = array();

    /**
     * 加载配置文件 位于 /Config/ 目录下
     * @param $name
     * @return bool
     */
    public static function loadConfig($name)
    {
        $name = ucfirst($name);
        if (isset(self::$_config[$name])) {
            return self::$_config[$name];
        }

        $path = BASE_DIR . '/Config/' . $name . '.php';
        if (!file_exists($path)) {
            return false;
        }

        self::$_config[$name] = require($path);
        return self::$_config[$name];
    }

}