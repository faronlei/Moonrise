<?php
/**
 * 异常处理
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

class Exception
{
    public static function initExceptionHandler()
    {
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
    }

    public static function exceptionHandler(Exception $e)
    {
        $exception_name = get_class($e);
        echo $e->getMessage();
        # todo 记录日志
    }
}