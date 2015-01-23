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
        $this->_values[$key] = $value;
    }
}