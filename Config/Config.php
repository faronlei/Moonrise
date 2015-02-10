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
    'csrf_protection' => false,

    'csrf_cookie_name' => 'mr_csrf_cookie',
    'csrf_token_name' => 'mr_csrf_token',
    'csrf_expire' => 7200,


);