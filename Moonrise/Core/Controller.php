<?php
/**
 * 控制器基类
 *
 * @author itsmikej
 */

namespace Moonrise\Core;

use Moonrise\Database\Eloquent;

abstract class Controller
{
    protected $request;

    public function __construct()
    {
        # todo 使用注册器
        $this->request = new Request();
        $this->request->unsetVars();
    }

    public function useEloquent($service)
    {
        # todo 缓存
        $eloquent = new Eloquent();
        $eloquent->init($service);
    }

    public function display($mode)
    {

    }
}