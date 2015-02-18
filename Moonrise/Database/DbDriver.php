<?php
/**
 * 数据驱动层
 *
 * @author itsmikej
 */

namespace Moonrise\Database;

abstract class DbDriver
{
    protected $db;

    protected $server_info;

    protected $config;

    /**
     * 执行时间
     * @var time_exec
     */
    protected $time_exec;

    /**
     * 查询语句
     * @var sql
     */
    protected $sql;

    static $db_cache = array();

    public function init($config)
    {
        $db_key = md5(json_encode($config));

        if (!isset(self::$db_cache[$db_key])) {
            $this->server_info = $config;
            # 连接数据库
            self::$db_cache[$db_key] = $this->connect(
                $config['hostname'],
                $config['username'],
                $config['password'],
                $config['database'],
                $config['port'],
                $config['flags'],
                $config['options']
            );
        }

        return self::$db_cache[$db_key];

    }

    abstract public function connect($hostname, $username, $password, $databases, $port=3306, $flags=null, $options=null);
    abstract public function re_connect();

    /**
     * 查询
     * @param $sql
     * @return \Moonrise\Database\DbResult
     */
    abstract public function query($sql);
    abstract public function close();
}