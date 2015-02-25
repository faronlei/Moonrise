<?php
/**
 * 控制器基类
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

abstract class Controller
{
    protected $request;

    public function __construct()
    {
        # todo 使用注册器
        $this->request = new Request();
    }

    public function display($mode)
    {

    }
}