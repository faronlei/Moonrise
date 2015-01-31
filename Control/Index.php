<?php
/**
 * 首页
 */

namespace Control;

use Moonrise\Core\Request;

class Index
{
    public function index()
    {
        # 使用注册器
        $a = new Request();
        $b = $a->request('mikej', 1);
        dump($b);
    }
}