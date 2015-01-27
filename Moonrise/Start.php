<?php
/**
 * 路由
 *
 * @author itsmikej
 */

namespace Moonrise;

use Moonrise\Component\Log;
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
        //throw new \Exception('mikej');

        Log::getInstance('aa')->log('test', array(), 'test/test');
        $route = $this->uri->getRequest();

        $class = str_replace(array('\\\\'), '\\', trim('Control\\'.$route['directory'].'\\'.$route['class'], '/'));

        $c = new $class;
        call_user_func_array(array($c, $route['method']), array($_GET));


    }
}