<?php
/**
 * 错误处理
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

use Moonrise\Component\Log;

class Error
{
    public static function initErrorHandler()
    {
        error_reporting(0);
        set_error_handler(array(__CLASS__, "errorHandler"));
        register_shutdown_function(array(__CLASS__, 'fatalErrorHandler'));
    }

    /**
     * 错误接管
     * @param $level
     * @param $message
     * @param $file
     * @param $line
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        $exit = false;
        switch($level) {
            # 注意级别
            case E_NOTICE:
            case E_USER_NOTICE:
                $error_type = 'Notice';
                break;
            # 警告级别
            case E_WARNING:
            case E_USER_WARNING:
                $error_type = 'Warning';
                break;
            # 错误级别
            case E_ERROR:
            case E_USER_ERROR:
            case E_COMPILE_ERROR:
            case E_RECOVERABLE_ERROR:
                $error_type = 'Fatal Error';
                $exit = true;
                break;
            # 解析错误
            case E_PARSE:
                $error_type = 'Parse Error';
                $exit = true;
                break;
            # php核心错误或警告
            case E_CORE_ERROR:
            case E_CORE_WARNING:
                $error_type = 'Core Problem';
                $exit = true;
                break;
            # 编译问题
            case E_COMPILE_ERROR:
            case E_COMPILE_WARNING:
                $error_type = 'Compile Problem';
                $exit = true;
                break;
            case E_STRICT:
                $error_type = 'Strict';
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $error_type = 'Deprecated';
                break;
            #其他末知错误
            default:
                $error_type = 'Unknown';
                $exit = true;
                break;
        }

        ob_start();
        printf("<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br/>".PHP_EOL, $error_type, $message, $file, $line);
        $error_info = ob_get_clean();

        if (MR_DEBUG) {
            echo $error_info . "<br/>\n";
        }

        # 记录request uri,同时写入日志
        if (isset($_SERVER['REQUEST_URI'])) {
            $error_info .= "=== {$_SERVER['REQUEST_METHOD']} {$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }
        Log::getInstance('error_handler')->errorHandler(strip_tags($error_info));

        # 如果错误影响到程序的正常执行，跳转到友好的错误提示页面
        if(true == $exit) {
            # echo '<script>location = "err.html"; </script>';
        }
    }

    /**
     * 捕捉致命错误
     */
    public static function fatalErrorHandler()
    {
        $e = error_get_last();
        switch($e['type']){
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                self::errorHandler($e['type'], $e['message'], $e['file'], $e['line']);
                break;
            default:
                break;
        }
    }
}