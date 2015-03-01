<?php
/**
 * Laravel Eloquent ORM 封装
 * ＃ todo 跨库查询
 *
 * @author itsmikej
 */

namespace Moonrise\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Moonrise\Core\Loader;

class Eloquent
{
    protected $config = array();

    public function __construct()
    {
    }

    public function addService($service)
    {
        $config = Loader::loadConfig('database');
        if (!isset($config['service'][$service])) {
            show_error('can not find '.$service.' config');
        }

        $service_config = $config['service'][$service];
        $this->config = array(
            'driver'    => 'mysql',
            'host'      => $service_config['hostname'],
            'database'  => $service_config['database'],
            'username'  => $service_config['username'],
            'password'  => $service_config['password'],
            'charset'   => $service_config['char_set'],
            'collation' => $service_config['db_collat'],
            'prefix'    => $service_config['db_prefix'],
        );
    }

    public function init($service)
    {
        $this->addService($service);

        # Eloquent ORM
        $capsule = new Capsule;

        $capsule->addConnection($this->config);

        $capsule->bootEloquent();
    }
}