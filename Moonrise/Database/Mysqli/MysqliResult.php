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

    protected $fetch_map = array(
        'assoc'    => MYSQLI_ASSOC,
        'num'      => MYSQLI_NUM,
        'both'     => MYSQLI_BOTH
    );

    public function __construct(\mysqli_result $mysqliRes)
    {
        $this->resultObject = $mysqliRes;
    }

    /**
     * 取出所有数据
     * @param string $mode_name
     * @return array|mixed
     */
    public function fetchAll($mode_name='assoc')
    {
        $mode = $this->getFetchMode($mode_name);
        if (method_exists($this->resultObject, 'fetch_all')) {
            $res = $this->resultObject->fetch_all($mode);
        } else {
            for ($res = array(); $tmp = $this->resultObject->fetch_array($mode);) {
                $res[] = $tmp;
            }
        }
        return $res;
    }

    /**
     * 取第$num行数据
     * @param $num
     * @param string $mode_name
     * @return mixed
     */
    public function result($num, $mode_name='both')
    {
        $mode = $this->getFetchMode($mode_name);
        $this->resultObject->data_seek($num);
        return $this->fetch_array($mode);
    }

    /**
     * 返回关联|索引
     * @param string $mode_name
     * @return mixed
     */
    public function fetchArray($mode_name='both')
    {
        $mode = $this->getFetchMode($mode_name);
        return $this->resultObject->fetch_array($mode);
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
     * @param string $class_name
     * @param array $params
     * @return object|\stdClass
     */
    public function fetchObject($class_name='', array $params=array())
    {
        if (isset($name)) {
            # 可返回一个实例化的类， $params 传递给构造函数
            return $this->resultObject->fetch_object($class_name, $params);
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

    /**
     * 获取fetch方式
     * @param $mode_name
     * @return mixed
     */
    public function getFetchMode($mode_name)
    {
        return isset($this->fetch_map[$mode_name]) ?
            $this->fetch_map[$mode_name] : $this->fetch_map['assoc'];
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