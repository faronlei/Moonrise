<?php
/**
 * 请求类
 * 不做数据过滤，由 Moonrise\Component\Filter 处理过滤
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

use Moonrise\Component\Filter;
use Moonrise\Component\Security;

class Request
{
    private $_vars = array(
        'g'   => array(),
        'p'   => array(),
        'c'   => array(),
        'r'   => array(),
        'f'   => array(),
    );

    private $_filter_vars = array(
        'g'   => array(),
        'p'   => array(),
        'c'   => array(),
        'r'   => array(),
        'f'   => array(),
    );

    private $_get;
    private $_post;
    private $_request;
    private $_cookie;
    private $_file;

    /**
     * Filter
     *
     * @var Filter
     */
    private $_filter;


    /**
     * Security
     *
     * @var Security
     */
    private $_Security;

    private $_feedback = array();


    public function __construct()
    {
        switch (MR_INTERFACE) {
            case 'web':
                # csrf 验证 todo 全局？
                if (Loader::loadConfig('config')['csrf_protection']) {
                    $this->_Security = Registry::getComponent('security');
                    $this->_Security->csrf_verify();
                }

                $this->_get     = & $_GET;
                $this->_post    = & $_POST;
                $this->_request = & $_REQUEST;
                $this->_cookie  = & $_COOKIE;
                $this->_file    = & $_FILES;
                break;
            case 'cli':
                # 将cli的参数解析到 _request
                foreach ($_SERVER['argv'] as $param) {
                    if (strpos($param, '=')) {
                        list($key, $value) = explode('=', $param);
                        $this->_request[$key] = $value;
                    }
                }
                break;
            default:
                break;
        }
    }

    /**
     * 注册变量
     * @param $variables
     */
    public function registerVars($variables)
    {
        foreach ($variables as $var_class=>$map) {
            switch ($var_class) {
                # 对于 request 的数据，分别注册到 get 和 post 下，使 get() post() 能取到数据
                case 'r':
                    $this->registerVars(array('g'=>$map));
                    $this->registerVars(array('p'=>$map));
                    break;
                default:
                    break;
            }
            foreach ($map as $name=>$value) {
                if (is_array($value) && empty($value[1])) {
                    $value[1] = null;
                }
                if (!is_array($value)) {
                    $value = array($value, null);
                }
                $this->_vars[$var_class][$name] = $value;
            }
        }
    }

    /**
     * 删除$_GET $_POST $REQUEST $COOKIE
     * @param string $vars
     */
    public function unsetVars($vars='gpr')
    {
        $l = strlen($vars);
        for ($i=0; $i<$l; $i++) {
            switch ($vars[$i]) {
                case 'g':
                    unset($_GET);
                    break;
                case 'p':
                    unset($_POST);
                    break;
                case 'r':
                    unset($_REQUEST);
                    break;
                case 'c':
                    unset($_COOKIE);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * 取$_GET参数
     * @param $name
     * @param null $var_type
     * @param array $options
     * @return array|null
     */
    public function get($name, $var_type=null, array $options=array())
    {
        return $this->_getVar('g', $name, $var_type, $options);
    }

    /**
     * 取$_POST参数
     * @param $name
     * @param null $var_type
     * @param array $options
     * @return array|null
     */
    public function post($name, $var_type=null, array $options=array())
    {
        return $this->_getVar('p', $name, $var_type, $options);
    }

    /**
     * 取$_REQUEST参数
     * @param $name
     * @param null $var_type
     * @param array $options
     * @return array|null
     */
    public function request($name, $var_type=null, array $options=array())
    {
        return $this->_getVar('r', $name, $var_type, $options);
    }

    /**
     * 取$_COOKIE参数
     * @param $name
     * @param null $var_type
     * @param array $options
     * @return array|null
     */
    public function cookie($name, $var_type=null, array $options=array())
    {
        return $this->_getVar('c', $name, $var_type, $options);
    }

    /**
     * 取$_FILE参数
     * @param $name
     * @param null $var_type
     * @param array $options
     * @return array|null
     */
    public function file($name, $var_type=null, array $options=array())
    {
        return $this->_getVar('f', $name, $var_type, $options);
    }


    private function _getVar($var_class, $name, $var_type=null, $options=array())
    {
        if (isset($this->_filter_vars[$var_class][$name])) {
            return $this->_filter_vars[$var_class][$name];
        }

        #if ($var_type !== null) {
            if (empty($options)) {
                $options = array();
            }
            $this->registerVars(array(
                $var_class => array(
                    $name => array($var_type, $options)
                )
            ));
        #}

        if (!isset($this->_vars[$var_class][$name])) {
            return null;
        } else {
            switch ($var_class) {
                case 'g':
                    $value = & $this->_get[$name];
                    break;
                case 'p':
                    $value = & $this->_post[$name];
                    break;
                case 'r':
                    $value = & $this->_request[$name];
                    break;
                case 'c':
                    $value = & $this->_cookie[$name];
                    break;
                case 'f':
                    $value = & $this->_file[$name];
                    break;
                default:
                    return null;
            }

            $var_params = $this->_vars[$var_class][$name];

            $this->_filter = Registry::getComponent('filter');
            $filter_value = $this->_filter->filter($value, $var_params[0], $var_params[1]);

            $this->_filter_vars[$var_class][$name] = $filter_value;

            return $filter_value;
        }
    }

    /**
     * 判断变量是否注册
     * @param $var_class
     * @param $name
     * @return bool
     */
    public function isRegistered($var_class, $name)
    {
        return isset($this->_vars[$var_class][$name]) ? true : false;
    }

    public function addFeedback($message)
    {
        array_push($this->_feedback, $message);
    }

    public function getFeedback()
    {
        return $this->_feedback;
    }

    public function getFeedbackString($separator=DIRECTORY_SEPARATOR)
    {
        return implode($separator, $this->_feedback);
    }
}