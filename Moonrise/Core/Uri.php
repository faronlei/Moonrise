<?php
/**
 * 解析URL
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

class Uri
{
    private $_config;

    private $_uri;

    private $_directory;

    private $_class;

    private $_method;

    public function __construct()
    {
        $this->_config = Loader::loadConfig('Routing');

        $this->fetchUri();

        $this->setRequest();
    }

    /**
     * 取Uri
     */
    protected function fetchUri()
    {
        if (MR_INTERFACE == 'cli') {
            # todo waiting...
            return;
        }

        if ($uri = $this->getUriFromQuestUri()) {
            $this->setUri($uri);
            return;
        }

        # PATH_INFO ???
        # QUERY_STRING ???

        $this->_uri = '';
        return;
    }

    /**
     * 设置uri
     * @param $str
     */
    protected function setUri($str)
    {
        # 过滤字符 todo 封装类
        $str = remove_invisible_characters($str, false);
        $this->_uri = ($str == '/') ? '' : $str;
    }

    /**
     * 取uri从$_SERVER_URI
     * @return mixed|string
     */
    protected function getUriFromQuestUri()
    {
        if (!isset($_SERVER['REQUEST_URI']) || !isset($_SERVER['SCRIPT_NAME'])) {
            return '';
        }

        $url = $_SERVER['REQUEST_URI'];

        if (strpos($url, $_SERVER['SCRIPT_NAME']) === 0) {
            # 兼容域名未重写
            $url = substr($url, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($url, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $url = substr($url, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }

        # 确保URL正确
        if (strncmp($url, '?/', 2) === 0) {
            $url = substr($url, 2);
        }

        $parts = preg_split('#\?#i', $url, 2);
        $url = $parts[0];
        if (isset($parts[1])) {
            $_SERVER['QUERY_STRING'] = $parts[1];
            parse_str($_SERVER['QUERY_STRING'], $_GET);
        } else {
            $_SERVER['QUERY_STRING'] = '';
            $_GET = array();
        }

        if ($url == '/' || empty($url)) {
            return '/';
        }

        $url = parse_url($url, PHP_URL_PATH);

        return str_replace(array('//', '../'), '/', trim($url, '/'));
    }

    /**
     * 过滤特殊字符
     * @param $str
     * @return mixed
     */
    protected function filterUri($str)
    {
        $bad = array('$', '(', ')', '%28', '%29');
        $good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');
        return str_replace($bad, $good, $str);
    }

    /**
     * 分割Uri保存到数组
     * @return array
     */
    protected function explodeUri()
    {
        $segments = array();
        $uris = explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->_uri));
        foreach ($uris as $val) {
            $val = trim($this->filterUri($val));
            if ($val != '') {
                $segments[] = ucfirst($val);
            }
        }
        return $segments;
    }

    /**
     * 设置请求
     */
    protected function setRequest()
    {
        $segments = $this->explodeUri();

        if (empty($segments)) {
            $this->setDirectory($this->_config['default_directory']);
            $this->setClass(ucfirst($this->_config['default_control']));
            $this->setMethod($this->_config['default_method']);
            return;
        }

        # Directory
        if (is_dir(BASE_DIR . '/Control/' . ($directory = implode('/', $segments)))) {
            $this->setDirectory($directory);
            $this->setClass(ucfirst($this->_config['default_control']));
            $this->setMethod($this->_config['default_method']);
            return;
        }

        # Directory/Class
        if (file_exists(BASE_DIR . '/Control/' . implode('/', $segments) . '.php')) {
            $this->setClass(array_pop($segments));
            $this->setDirectory(implode('/', $segments));
            $this->setMethod('index');
            return;
        }

        # Directory/Class/Method
        $method = strtolower(array_pop($segments));
        if (file_exists(BASE_DIR . '/Control/' . implode('/', $segments) . '.php')) {
            $this->setClass(array_pop($segments));
            $this->setMethod($method);
            $this->setDirectory(array_pop($segments));
            return;
        }

        # todo fix this
        array_push($segments, $method);
        echo '{'.implode('/', $segments).'}: Not Found!';die;
    }

    public function getRequest()
    {
        return array(
            'directory' => $this->getDirectory(),
            'class'     => $this->getClass(),
            'method'    => $this->getMethod()
        );
    }

    protected function setDirectory($directory)
    {
        $this->_directory = $directory;
    }

    protected function getDirectory()
    {
        return $this->_directory;
    }

    protected function setClass($class)
    {
        $this->_class = $class;
    }

    protected function getClass()
    {
        return $this->_class;
    }

    protected function setMethod($method)
    {
        $this->_method = $method;
    }

    protected function getMethod()
    {
        return $this->_method;
    }
}