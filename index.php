<?php
/**
 * 入口文件
 *
 * @author itsmikej
 */

# todo 整合入口所有定义

define('MR_TIMESTAMP', time());
define('MR_DATETIME', date('Y-m-d H:i:s', MR_TIMESTAMP));

define('BASE_DIR', __DIR__);
define('APPDATA_DIR', BASE_DIR . '/Appdata');

$loader = require(BASE_DIR . '/vendor/autoload.php');
require(BASE_DIR . '/Moonrise/Core/Loader.php');
require(BASE_DIR . '/Moonrise/Functions/Common.php');

# todo 测试单元
//$loader->add('Moonrise\\', __DIR__);
//$loader->add('Control\\', __DIR__);

spl_autoload_register("\\Moonrise\\Core\\Loader::AutoLoad");

if (PHP_SAPI == 'cli' || defined('STDIN')) {
    define('MR_INTERFACE', 'cli');
} else {
    define('MR_INTERFACE', 'web');
}

define('MR_DEBUG', true);

$app = new Moonrise\Start();

$app->run();