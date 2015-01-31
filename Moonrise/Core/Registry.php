<?php
/**
 * 全局注册器
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

class Registry
{
    private static $_instance;
    private $_values = array();

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function get($key)
    {
        if (isset($this->_values[$key])) {
            return $this->_values[$key];
        }
        return null;
    }

    public function set($key, $value)
    {
        if (isset($this->_values[$key])) {
            return false;
        }
        $this->_values[$key] = $value;
    }

    public static function getComponent($name)
    {
        self::checkName($name);
        if (null === ($component = self::getInstance()->get($name))) {
            $class_name = 'Moonrise\\Component\\' . ucfirst($name);
            $component = new $class_name;
            self::getInstance()->set($name, $component);
        }

        return $component;
    }

    protected static function checkName($name)
    {
        return 1;
    }
}