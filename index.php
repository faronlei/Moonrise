<?php
/**
 * 入口文件
 *
 * @author itsmikej
 */

define('MR_TIMESTAMP', time());
define('MR_DATETIME', date('Y-m-d H:i:s', MR_TIMESTAMP));

define('BASE_DIR', __DIR__);
define('APPDATA_DIR', BASE_DIR . '/Appdata');

# 运行方式
if (PHP_SAPI == 'cli' || defined('STDIN')) {
    define('MR_INTERFACE', 'cli');
} else {
    define('MR_INTERFACE', 'web');
}

define('MR_DEBUG', true);

require(BASE_DIR . '/Moonrise/Constants.php');
require(BASE_DIR . '/vendor/autoload.php');
require(BASE_DIR . '/Moonrise/Core/Loader.php');
require(BASE_DIR . '/Moonrise/Functions/Common.php');

spl_autoload_register("\\Moonrise\\Core\\Loader::AutoLoad");

$app = new Moonrise\Start();

$app->run();