<?php
/**
 * 路由
 *
 * @author itsmikej
 */

namespace Moonrise;

use Moonrise\Core\Error;
use Moonrise\Core\Exception;
use Moonrise\Core\Uri;

class Start
{
    /**
     * @var Uri
     */
    protected $uri;

    public function __construct()
    {
        $this->_init();
        $this->uri = new Uri();
    }

    private function _init()
    {
        # 错误处理
        Error::initErrorHandler();
        # 异常处理
        Exception::initExceptionHandler();

    }

    public function run()
    {
        $route = $this->uri->getRequest();

        # shit
        if (isset($route['directory'])) {
            $route['directory'] = str_replace('/', '\\', $route['directory']);
        }
        $class = str_replace(array('\\\\'), '\\', trim('Control\\'.$route['directory'].'\\'.$route['class'], '/'));

        $c = new $class;
        call_user_func_array(array($c, $route['method']), array());


    }
}