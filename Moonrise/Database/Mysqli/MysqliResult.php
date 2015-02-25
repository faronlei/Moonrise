<?php
/**
 * Mysqli结果集
 *
 * @author itsmikej
 */

namespace Moonrise\Database\Mysqli;

use Moonrise\Database\DbResult;

class MysqliResult extends DbResult
{
    protected $resultObject;

    public $sql;
    public $time_exec;
    public $affect_rows;
    public $error;
    public $errno;

    public function __construct(\mysqli_result $mysqliRes)
    {
        $this->resultObject = $mysqliRes;
    }

    /**
     * 取出所有数据
     * @param int $type
     * @return array|mixed
     */
    public function fetchAll($type=MYSQLI_ASSOC)
    {
        if (method_exists($this->resultObject, 'fetch_all')) {
            $res = $this->resultObject->fetch_all($type);
        } else {
            for ($res = array(); $tmp = $this->resultObject->fetch_array($type);) {
                $res[] = $tmp;
            }
        }
        return $res;
    }

    /**
     * 取第$num行数据
     * @param $num
     * @param int $type
     * @return mixed
     */
    public function result($num, $type=MYSQLI_BOTH)
    {
        $this->resultObject->data_seek($num);
        return $this->fetch_array($type);
    }

    /**
     * 返回关联|索引
     * @param int $type
     * @return mixed
     */
    public function fetchArray($type=MYSQLI_BOTH)
    {
        return $this->resultObject->fetch_array($type);
    }

    /**
     * 关联数组返回一行
     * @return array
     */
    public function fetchAssoc()
    {
        return $this->resultObject->fetch_assoc();
    }

    /**
     * 索引数组返回一行
     * @return mixed
     */
    public function fetchRow()
    {
        return $this->resultObject->fetch_row();
    }

    /**
     * 返回一个对象，可指定要实例化的类
     * @param string $classname
     * @param array $params
     * @return object|\stdClass
     */
    public function fetchObject($classname='', array $params=array())
    {
        if (isset($name)) {
            # 可返回一个实例化的类， $params 传递给构造函数
            return $this->resultObject->fetch_object($classname, $params);
        } else {
            return $this->resultObject->fetch_object();
        }

    }

    /**
     * 获取一个字段的信息
     * @return object
     */
    public function fetchField()
    {
        return $this->resultObject->fetch_field();
    }

    /**
     * 获取所有字段的信息
     * @return array
     */
    public function fetchFields()
    {
        return $this->resultObject->fetch_fields();
    }

    /**
     * 获取一个字段详细信息
     * @param $num
     * @return object
     */
    public function fetchFieldDirect($num)
    {
        return $this->resultObject->fetch_field_direct($num);
    }

    /**
     * 释放内存
     */
    public function free()
    {
        $this->resultObject->free();
    }

    public function __get($name)
    {
        if (isset($this->resultObject->$name)) {
            return $this->resultObject->$name;
        } else {
            return false;
        }
    }

}