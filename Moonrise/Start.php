<?php
/**
 * 路由
 *
 * @author itsmikej
 */

namespace Moonrise;

use Moonrise\Core\Uri;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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


        # 异常处理
    }

    public function run()
    {
        $route = $this->uri->getRequest();

        $class = str_replace(array('\\\\'), '\\', trim('Control\\'.$route['directory'].'\\'.$route['class'], '/'));

        $c = new $class;
        call_user_func_array(array($c, $route['method']), array($_GET));


        // create a log channel
        $log = new Logger('mikej');
        $log->pushHandler(new StreamHandler(BASE_DIR . '/Appdata/test.log', Logger::WARNING));

        // add records to the log
        $log->addWarning('Foo', array(1,2,3));
        $log->addError('内容不合法');

    }
}