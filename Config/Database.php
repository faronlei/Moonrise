<?php

/**
 * 数据库配置
 *
 * todo 考虑静态文件解析配置，更安全
 */

return array(

    'service' => array(

        'default' => array(
            'hostname'  => 'localhost',
            'username'  => 'root',
            'password'  => 'secret',
            'database'  => 'itsmikej',
            'port'      => 3306,
            'db_driver' => 'mysqli',
            'db_prefix' => '',
            'char_set'  => 'utf8',
            'db_collat' => 'utf8_general_ci',
            'options'   => array(),
            'flags'     => array()
        ),
        'pdotest' => array(
            'hostname'  => 'localhost',
            'username'  => 'root',
            'password'  => 'secret',
            'database'  => 'itsmikej',
            'port'      => 3306,
            'db_driver' => 'pdo',
            'db_prefix' => '',
            'char_set'  => 'utf8',
            'db_collat' => 'utf8_general_ci',
            'options'   => array(),
            'flags'     => array()
        ),
        # ...
    )

);