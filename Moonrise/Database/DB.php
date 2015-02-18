<?php
/**
 * 数据库 bootstrap
 * 取配置 入配置
 *
 * @author itsmikej
 */

namespace Moonrise\Database;

use Moonrise\Core\Loader;

class DB
{
    public static function bootStrap($service)
    {
        # 取配置
        $config = Loader::loadConfig('database');
        if (!isset($config['service'][$service])) {
            show_error('can not find '.$service.' config');
        }

        $service_config = $config['service'][$service];
        $driver = ucfirst($service_config['db_driver']);

        $driverClass = 'Moonrise\\Database\\' . $driver .'\\'. $driver . 'Driver';
        $db = new $driverClass;

        if (true) {
            $db->init($service_config);
        }

        return $db;
    }
}