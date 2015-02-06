<?php
/**
 * 系统级定义
 *
 * @author itsmikej
 */

/**
 * 变量类型
 */
define('MR_TYPE_DEFAULT',        -1);
define('MR_TYPE_NOCLEAN',        0);
define('MR_TYPE_BOOL',           1);
define('MR_TYPE_STR',            2);
define('MR_TYPE_INT',            3);
define('MR_TYPE_UINT',           4);
define('MR_TYPE_NUM',            5);
define('MR_TYPE_UNUM',           6);
define('MR_TYPE_DATETIME',       7);
define('MR_TYPE_JSON',           8);

define('MR_TYPE_ARRAY',              100);
define('MR_TYPE_ARRAY_BOOL',         101);
define('MR_TYPE_ARRAY_STR',          102);
define('MR_TYPE_ARRAY_INT',          103);
define('MR_TYPE_ARRAY_UINT',         104);
define('MR_TYPE_ARRAY_NUM',          105);
define('MR_TYPE_ARRAY_UNUM',         106);
define('MR_TYPE_ARRAY_DATETIME',     107);
define('MR_TYPE_ARRAY_JSON',         108);

define('MR_TYPE_ENUM',               200);
define('MR_TYPE_ENUM_BY_KEYS',       201);

define('MR_TYPE_FILE',               300);