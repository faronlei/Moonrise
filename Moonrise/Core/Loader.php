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
        $file_path = BASE_DIR . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file_path)) {
            require($file_path);
        }
        return false;
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

    /**
     * 加载Model
     * @param $name
     * @return mixed
     */
    public static function loadModel($name)
    {
        if (!$name) {
            $name = self::getDefaultName();
        }
        $path = BASE_DIR . '/Model/' . $name . '.php';
        if (!file_exists($path)) {
            $message = 'model file not exits!';
            if (MR_DEBUG) {
                $message = $path . ' ' . $message;
            }
            show_error($message);
        }
        $class = '\\Model\\' . str_replace('/', '\\', $name);
        return new $class;
    }

    /**
     * 加载view
     * @param $name
     */
    public static function loadView($name=null)
    {
        if (!$name) {
            $name = self::getDefaultName();
        }
        $path = BASE_DIR . '/View/' . $name . '.php';
        if (!file_exists($path)) {
            $message = 'view file not exits!';
            if (MR_DEBUG) {
                $message = $path . ' ' . $message;
            }
            show_error($message);
        }
        require($path);
    }

    /**
     * 加载默认文件路径
     * @return string
     */
    protected static function getDefaultName()
    {
        $route = Registry::getInstance()->get('route');
        return trim($route['directory'] . '/' . $route['class'], '/');
    }

}