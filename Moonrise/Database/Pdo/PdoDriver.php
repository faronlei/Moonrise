<?php
/**
 * Pdo 数据驱动
 *
 * @author itsmikej
 */

namespace Moonrise\Database\Pdo;

use Moonrise\Database\DbDriver;

class PdoDriver extends DbDriver
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $driver='mysql';

    /**
     * 连接数据库
     * @param $hostname
     * @param $username
     * @param $password
     * @param $databases
     * @param int $port
     * @param null $flags
     * @param null $options
     * @return \PDO
     */
    public function connect($hostname, $username, $password, $databases, $port=3306, $flags=null, $options=null)
    {
        $dsn = $this->driver.":host={$hostname};dbname={$databases};port={$port}";

        $this->db = new \PDO($dsn, $username, $password, $options);

        return $this->db;
    }

    /**
     * 设置驱动 默认为mysql
     * @param $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * 重新连接数据库
     */
    public function reConnect()
    {
        $this->init($this->config);
    }

    /**
     * 执行简单的查询，返回的对象可迭代
     * @param $sql
     * @return \PDOStatement
     */
    public function simpleQuery($sql)
    {
        $this->sql = $sql;
        # todo 处理sql语句
        $time_begin = microtime(true);
        $result = $this->db->query($sql);
        $this->time_exec = microtime(true) - $time_begin;
        return $result;
    }

    /**
     * 查询
     * @param $sql
     * @return PdoResult
     */
    public function query($sql)
    {
        if (!$this->db) {
            $this->reConnect();
        }
        $res = new PdoResult($this->simpleQuery($sql));

        $res->time_exec     = $this->time_exec;
        $res->error         = $this->error();
        $res->errno         = $this->errno();
        $res->sql           = $this->sql;

        return $res;
    }

    /**
     * 错误信息
     * @return array
     */
    public function error()
    {
        return $this->db->errorInfo();
    }

    /**
     * 错误号
     * @return mixed
     */
    public function errno()
    {
        return $this->db->errorCode();
    }

    /**
     * 预处理sql
     * @param $sql
     * @param array $driver_options
     * 只进游标:   PDO::CURSOR_FWDONLY
     * 可滚动游标: PDO::CURSOR_SCROLL
     *
     * @return \PDOStatement
     */
    public function prepare($sql, $driver_options=array(\PDO::ATTR_CURSOR=>\PDO::CURSOR_FWDONLY))
    {
        return $this->db->prepare($sql, $driver_options);
    }

    /**
     * 绑定参数
     * @param \PDOStatement $stmt
     * @param array $params
     */
    public function bindParam(\PDOStatement $stmt, array $params=array())
    {
        foreach ($params as $key => &$value) {
            if (is_integer($key)) {
                $key++;
            }
            $stmt->bindParam($key, $value);
        }
    }

    /**
     * 绑定数值，可传递变量和值
     * 关于bindParam和bindValue区别
     * http://stackoverflow.com/questions/1179874/pdo-bindparam-versus-bindvalue
     * @param \PDOStatement $stmt
     * @param array $params
     */
    public function bindValue(\PDOStatement $stmt, array $params=array())
    {
        foreach ($params as $key => $value) {
            if (is_integer($key)) {
                $key++;
            }
            $stmt->bindValue($key, $value);
        }
    }

    /**
     * 执行一条预处理语句
     * @param \PDOStatement $stmt
     * @param array $params
     * @return bool
     */
    public function execute(\PDOStatement $stmt, array $params=array())
    {
        if ($params) {
            return $stmt->execute($params);
        }
        return $stmt->execute();
    }

    /**
     * 将结果集中的一列绑定到一个变量
     * prepare -> [bind_param or bind_value] -> execute -> bind_Column -> fetch
     * @param \PDOStatement $stmt
     * @param array $params
     */
    public function bindColumn(\PDOStatement $stmt, array $params=array())
    {
        foreach ($params as $key => $value) {
            $stmt->bindColumn($key, $value);
        }
    }

    /**
     * select 预处理快捷查询
     * @param $sql
     * @param array $params
     * @return array|bool|null
     */
    public function setResultQuery($sql, array $params=array())
    {
        $array = null;
        $stmt = $this->setStatement($sql, $params);

        try {
            if ($this->execute($stmt)) {
                $array = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            $array = false;
        }

        return $array;
    }

    /**
     * 无结果快捷查询
     * @param $sql
     * @param array $params
     * @return bool
     */
    public function setNoResultQuery($sql, array $params=array())
    {
        $validation = false;

        $stmt = $this->setStatement($sql, $params);

        try {
            if ($this->execute($stmt)) {
                $validation = true;
            }
        } catch (\Exception $e) {
            $validation = false;
        }

        return $validation;
    }

    /**
     * 初始化预处理查询
     * @param $sql
     * @param $params
     * @return \PDOStatement
     */
    public function setStatement($sql, $params)
    {
        $stmt = $this->prepare($sql);

        if (count($params) != 0) {
            $this->bindParam($stmt, $params);
        }
        return $stmt;
    }

    ###############事务相关################
    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollBack();
    }
    ######################################

    /**
     * 关闭数据库
     * @return bool
     */
    public function close()
    {
        if ($this->db) {
            unset($this->db);
        }
        return true;
    }

    public function __destruct()
    {
        $this->close();
    }
}