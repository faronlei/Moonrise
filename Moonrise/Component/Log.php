<?php
/**
 * 日志组件
 *
 * @author itsmikej
 */

namespace Moonrise\Component;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\WebProcessor;
use Moonrise\Core\Loader;

class Log
{
    private static $_instance = array();
    private static $_config;

    /**
     * @var Logger
     */
    protected $logger;

    private function __construct(){}

    public static function getInstance($channel)
    {
        if (!isset(self::$_instance[$channel])) {
            self::$_config = Loader::loadConfig('log');
            if (!in_array($channel, self::$_config['channel'])) {
                $channel = LOG_CHANNEL_DEFAULT;
            }
            self::$_instance[$channel] = new Log();
            self::$_instance[$channel]->logger = new Logger($channel);
        }
        return self::$_instance[$channel];
    }


    /**
     * 记录错误信息
     * @param $message
     * @param array $context
     */
    public function errorHandler($message, $context=array())
    {
        $path = BASE_DIR.'/Appdata/error_handler/' . $this->genDateDir() . DIRECTORY_SEPARATOR . $this->getLogFileName();
        if (!file_exists($path)) {
            mkdir(dirname($path), 0777, true);
            file_put_contents($path, '');
        }

        $this->logger->pushHandler(new StreamHandler($path, Logger::INFO));
        # $this->logger->pushProcessor(new WebProcessor());
        $this->logger->addInfo($message, $context);

    }


    /**
     * 生成日期目录 如: 2015-01/01-27
     * @return string
     */
    protected function genDateDir()
    {
        return date("Y-m", MR_TIMESTAMP) . DIRECTORY_SEPARATOR . date('m-d', MR_TIMESTAMP);
    }

    /**
     * 返回日志文件名
     * @return string
     */
    protected function getLogFileName()
    {
        return 'information.log';
    }


}