<?php
/**
 * 一些系统定义
 */


return array(

    'charset' => 'UTF-8',

    # cookie 相关
    'cookie_prefix' => '',
    'cookie_domain' => '',
    'cookie_path' => '/',
    'cookie_secure' => false, # 只在HTTPS连接时生效


    # CSRF相关
    'csrf_protection' => true,

    'csrf_cookie_name' => '',
    'csrf_token_name' => '',
    'csrf_expire' => 7200,


);