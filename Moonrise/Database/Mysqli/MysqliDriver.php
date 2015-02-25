<?php
/**
 * Mysqli 数据驱动
 *
 * @author itsmikej
 */


namespace Moonrise\Database\Mysqli;

use Moonrise\Database\DbDriver;

class MysqliDriver extends DbDriver
{

    /**
     * db
     *
     * @var \mysqli
     */
    protected $db;

    public function __construct()
    {
        $this->db = mysqli_init();
    }

    private function _options($option, $value)
    {
        return $this->db->options($option, $value);
    }

    /**
     * 连接数据库
     * @param $hostname
     * @param $username
     * @param $password
     * @param $database
     * @param int $port
     * @param null $flag
     * @param null $options
     * @return bool
     */
    public function connect($hostname, $username, $password, $database, $port=3306, $flag=null, $options=null)
    {
        $flag = 0;
        if (isset($flag) && is_array($flag) && $flag) {
            if (isset($flags['client_compress'])) {
                $flag |= MYSQLI_CLIENT_COMPRESS;
            }
            if (isset($flags['client_ignore_space'])) {
                $flag |= MYSQLI_CLIENT_IGNORE_SPACE;
            }
            if (isset($flags['client_ssl'])) {
                $flag |= MYSQLI_CLIENT_SSL;
            }
            if (isset($flags['client_interactive'])) {
                $flag |= MYSQLI_CLIENT_INTERACTIVE;
            }
            if (isset($flags['client_found_rows'])) {
                $flag |= MYSQLI_CLIENT_FOUND_ROWS;
            }
        }

        if (isset($options) && is_array($options) && $options) {
            if (isset($options['opt_connect_timeout'])) {
                $this->_options(MYSQLI_OPT_CONNECT_TIMEOUT, $options['opt_connect_timeout']);
            }
            if (isset($options['opt_local_infile'])) {
                $this->_options(MYSQLI_OPT_LOCAL_INFILE, $options['opt_local_infile']);
            }
            if (isset($options['init_command'])) {
                $this->_options(MYSQLI_INIT_COMMAND, $options['init_command']);
            }
            if (isset($options['read_default_file'])) {
                $this->_options(MYSQLI_READ_DEFAULT_FILE, $options['read_default_file']);
            }
            if (isset($options['read_default_group'])) {
                $this->_options(MYSQLI_READ_DEFAULT_GROUP, $options['read_default_group']);
            }
        }

        return $this->db->real_connect($hostname, $username, $password, $database, $port, null, $flag);
    }

    /**
     * 设置编码
     * @param $char_set
     */
    protected function setCharset($char_set)
    {
        $this->server_info['char_set'] = $char_set;
        $this->db->set_charset($char_set);
    }

    /**
     * 选择数据库
     * @param $database
     * @return bool
     */
    public function selectDb($database)
    {
        if ($this->server_info['database'] == $database) {
            return true;
        }
        $this->server_info['database'] = $database;
        $this->db->select_db($database);
    }

    /**
     * 重连数据库
     */
    public function reConnect()
    {
        $this->init($this->config);
    }

    /**
     * ping
     * @return bool
     */
    public function ping()
    {
        return $this->db->ping();
    }

    /**
     * 简单的查询，直接返回\mysqli_result对象
     * @param $sql
     * @return bool|\mysqli_result
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
     * @return MysqliResult
     */
    public function query($sql)
    {
        if (!$this->db && !$this->ping()) {
            $this->reConnect();
        }

        $res = new MysqliResult($this->simpleQuery($sql));

        $res->time_exec     = $this->time_exec;
        $res->affect_rows   = $this->affectedRows();
        $res->error         = $this->error();
        $res->errno         = $this->errno();
        $res->sql           = $this->sql;

        return $res;
    }

    /**
     * 影响行数
     * @return int
     */
    public function affectedRows()
    {
        return $this->db->affected_rows;
    }

    /**
     * mysqli 错误
     * @return string
     */
    protected function error()
    {
        return $this->db->error;
    }

    /**
     * mysqli 错误号
     * @return mixed
     */
    public function errno()
    {
        return $this->db->errno;
    }

    ################事务相关##################
    public function autocommit($mode=false)
    {
        $this->db->autocommit($mode);
    }

    public function beginTransaction()
    {
        $this->db->begin_transaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollback();
    }
    ########################################


    /**
     * 准备预处理语句
     * @param $sql
     * @return \mysqli_stmt
     */
    public function prepare($sql)
    {
        return $this->db->prepare($sql);
    }

    /**
     * 绑定参数
     * @param \mysqli_stmt $stmt
     * @param array $params
     */
    public function bindParam(\mysqli_stmt $stmt, array $params)
    {
        call_user_func_array(array($stmt, 'bind_param'), $params);
    }

    /**
     * 执行一个预处理查询
     * @param \mysqli_stmt $stmt
     */
    public function execute(\mysqli_stmt $stmt)
    {
        $stmt->execute();
    }

    /**
     * 绑定结果到变量
     * @param \mysqli_stmt $stmt
     * @param array $params
     */
    public function bindResult(\mysqli_stmt $stmt, array $params)
    {
        $stmt->store_result();
        call_user_func_array(array($stmt, 'bind_result'), $params);
    }

    /**
     * 预处理查询语句
     * prepare -> bind_param -> execute -> store_result -> bind_result -> fetch
     *
     * @param $sql
     * @param $param  array('idsb', &$foo, &$bar ...)
     * @return bool|null
     */
    public function setResultQuery($sql, $param)
    {
        $array = null;
        if (!$this->db->connect_errno) {

            $stmt = $this->setStatement($sql, $param);

            try {
                if ($stmt->execute()) {

                    # store result
                    $stmt->store_result();

                    $var = array();
                    $data = array();

                    # 取字段信息
                    $meta = $stmt->result_metadata();

                    while ($field = $meta->fetch_field()) {
                        $var[] = & $data[$field->name];
                    }

                    call_user_func_array(array($stmt, 'bind_result'), $var);

                    $i = 0;

                    # 取出结果 到 绑定的变量中
                    # $stmt->data_seek(2); # 移动数据库指针
                    while ($stmt->fetch()) {
                        $array[$i] = array();
                        foreach ($data as $k=>$v) {
                            $array[$i][$k] = $v;
                        }
                        $i++;
                    }

                    $stmt->free_result();
                    $stmt->close();

                }
            } catch (\Exception $e) {
                $array = false;
            }

        }

        return $array;
    }

    /**
     * 非查询的预处理
     * @param $sql
     * @param $param
     * @return bool
     */
    public function setNoResultQuery($sql, $param)
    {
        $validation = false;

        if (!$this->db->connect_errno) {

            $stmt = $this->setStatement($sql, $param);

            try {
                if ($stmt->execute()) {

                    $stmt->close();
                    $validation = true;

                }
            } catch (\Exception $e) {
                $validation = false;
            }
        }

        return $validation;

    }


    /**
     * prepare一个stmt，并绑定
     * @param $sql
     * @param $param
     * @return \mysqli_stmt
     */
    public function setStatement($sql, $param)
    {
        try {
            $stmt = $this->prepare($sql);

            $ref = new \ReflectionClass('mysqli_stmt');

            if (count($param) != 0) {
                $method = $ref->getMethod('bind_param');
                $method->invokeArgs($stmt, $param);
            }

        } catch (\Exception $e) {
            if ($stmt) {
                $stmt->close();
            }
        }

        return $stmt;
    }

    /**
     * 关闭数据库
     */
    public function close()
    {
        if ($this->db) {
            return $this->db->close();
        } else {
            return true;
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
