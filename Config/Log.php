<?php
/**
 * 日志配置
 */

define('LOG_CHANNEL_DEFAULT', 'default');

define('LOG_CHANNEL_ERROR_HANDLER', 'error_handler');

define('LOG_CHANNEL_EXCEPTION', 'exception');

define('LOG_CHANNEL_DB', 'database');


return array(
    'channel' => array(
        LOG_CHANNEL_DEFAULT,
        LOG_CHANNEL_ERROR_HANDLER,
        LOG_CHANNEL_DB
    )
);