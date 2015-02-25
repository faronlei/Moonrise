<?php
/**
 * PDO结果集
 *
 * @author itsmikej
 */

namespace Moonrise\Database\Pdo;

use Moonrise\Database\DbResult;

class PdoResult extends DbResult
{
    protected $resultObject;

    public $sql;
    public $time_exec;
    public $affect_rows;
    public $error;
    public $errno;

    # 获取结果方式
    protected $fetch_map = array(
        'assoc'   => \PDO::FETCH_ASSOC,
        'num'     => \PDO::FETCH_NUM,
        'both'    => \PDO::FETCH_BOTH,
        'bound'   => \PDO::FETCH_BOUND,
        'lazy'    => \PDO::FETCH_LAZY,   # 将结果集中的每一行作为一个对象返回
        'named'   => \PDO::FETCH_NAMED,
        'obj'     => \PDO::FETCH_OBJ,
        'class'   => \PDO::FETCH_CLASS,  # 返回一个所请求类的新实例
        'info'    => \PDO::FETCH_INTO,   # 更新一个请求类的现有实例
        'func'    => \PDO::FETCH_FUNC    # 用于自定义的方式处理数据 仅用于fetchAll()
    );

    public function __construct(\PDOStatement $PDOStatement)
    {
        $this->resultObject = $PDOStatement;
        $this->affect_rows = $this->affectRows();
    }

    /**
     * 设置fetch结果方式
     * @param string $mode_name
     */
    public function setFetchMode($mode_name='assoc')
    {
        $mode = $this->getFetchMode($mode_name);
        $this->resultObject->setFetchMode($mode);
    }

    public function getResultObject()
    {
        return $this->resultObject;
    }

    /**
     * 查询影响行数
     * @return int
     */
    public function affectRows()
    {
        return $this->resultObject->rowCount();
    }

    /**
     * 其他参数见官方文档，可使用 $this->getResultObject 得到 PDOStatement 对象 再做处理
     * @doc http://php.net/manual/zh/pdostatement.fetchall.php
     * @param string $mode_name
     * @return array
     */
    public function fetchAll($mode_name='assoc')
    {
        $mode = $this->getFetchMode($mode_name);
        return $this->resultObject->fetchAll($mode);
    }

    /**
     * 获取下一行并作为一个对象返回
     * 此方法是使用 PDO::FETCH_CLASS 或 PDO::FETCH_OBJ 风格的 PDOStatement::fetch() 的一种替代。
     * @param $class_name
     * @param array $args
     * @return mixed
     */
    public function fetchObject($class_name, array $args=array())
    {
        return $this->resultObject->fetchObject($class_name, $args);
    }

    /**
     * http://php.net/manual/zh/pdostatement.fetch.php
     * @param string $mode_name
     * @return mixed
     */
    public function fetch($mode_name='assoc')
    {
        $mode = $this->getFetchMode($mode_name);
        return $this->resultObject->fetch($mode);
    }

    public function getFetchMode($mode_name)
    {
        return isset($this->fetch_map[$mode_name]) ?
            $this->fetch_map[$mode_name] : $this->fetch_map['assoc'];

    }



}