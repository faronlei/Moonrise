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

    protected function set_charset($char_set)
    {
        $this->server_info['char_set'] = $char_set;
        $this->db->set_charset($char_set);
    }

    public function select_db($database)
    {
        if ($this->server_info['database'] == $database) {
            return true;
        }
        $this->server_info['database'] = $database;
        $this->db->select_db($database);
    }

    public function re_connect()
    {
        $this->init($this->config);
    }

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


    public function query($sql)
    {
        if (!$this->db && !$this->ping()) {
            $this->re_connect();
        }

        $res = new MysqliResult($this->simpleQuery($sql));

        $res->time_exec     = $this->time_exec;
        $res->affect_rows   = $this->affected_rows();
        $res->error         = $this->error();
        $res->errno         = $this->errno();
        $res->sql           = $this->sql;

        return $res;
    }

    /**
     * 影响行数
     * @return int
     */
    public function affected_rows()
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

    public function begin_transaction()
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
