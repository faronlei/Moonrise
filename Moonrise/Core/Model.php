<?php
/**
 * 模型基类
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

use Moonrise\Database\DB;

class Model
{
    protected $service;

    protected $db;

    public function __construct()
    {

    }

    static $driver=array();

    /**
     * 连接数据库
     *
     * @param null $service
     * @return \Moonrise\Database\DbDriver
     */
    protected function connectDB($service=null)
    {
        if (!$service) {
            $service = $this->service;
        }

        if (!isset(self::$driver[$service])) {
            self::$driver[$service] = DB::bootStrap($service);
        }
        return self::$driver[$service];


    }
}