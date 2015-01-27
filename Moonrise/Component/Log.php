<?php
/**
 * 日志组件
 *
 * @author itsmikej
 */

namespace Moonrise\Component;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
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
        $path = APPDATA_DIR . '/error_handler/' . $this->genDirPiece();

        $this->generatePath($path);

        $this->logger->pushHandler(new StreamHandler($path, Logger::NOTICE));
        # $this->logger->pushProcessor(new WebProcessor());
        $this->logger->addNotice($message, $context);
    }

    /**
     * 异常处理
     * @param \Exception $e
     * @param $class_name
     */
    public function exceptionHandler(\Exception $e, $class_name)
    {
        $path = APPDATA_DIR . '/exception/' . $this->genDirPiece();

        $this->generatePath($path);

        $this->logger->pushHandler(new StreamHandler($path, Logger::INFO));

        $message = $class_name . ': ' . $e->getMessage();
        $context = array(
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        );
        $this->logger->addInfo($message, $context);
    }

    /**
     * 记录日志
     * @param $message
     * @param array $context
     * @param $path
     * @param int $level
     */
    public function log($message, $context=array(), $path, $level=Logger::INFO)
    {
        $path = $this->getPath($path);
        $this->generatePath($path);

        $this->logger->pushHandler(new StreamHandler($path, $level));
        $this->logger->pushHandler(new FirePHPHandler());
        $this->logger->addInfo($message, $context);
    }

    /**
     * 完整路径
     * @param $path
     * @return string
     */
    protected function getPath($path)
    {
        $ds = DIRECTORY_SEPARATOR;
        return APPDATA_DIR . $ds . $path . $ds . $this->genDirPiece();
    }

    /**
     * 创建文件
     * @param $path
     * @return int
     */
    protected function generatePath($path)
    {
        if (!file_exists($path)) {
            mkdir(dirname($path), 0777, true);
            return file_put_contents($path, '');
        }
    }

    /**
     * 生成部分目录 如: 2015-01/01-27/information.log
     * @return string
     */
    protected function genDirPiece()
    {
        $piece = date("Y-m", MR_TIMESTAMP) . DIRECTORY_SEPARATOR . date('m-d', MR_TIMESTAMP);
        $piece .= DIRECTORY_SEPARATOR . $this->getLogFileName();

        return $piece;
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