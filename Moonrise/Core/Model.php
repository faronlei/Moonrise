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
     * @return \Moonrise\Database\DbDriver
     */
    protected function connectDB()
    {
        if (!isset(self::$driver[$this->service])) {
            self::$driver[$this->service] = DB::bootStrap($this->service);
        }
        return self::$driver[$this->service];


    }
}