<?php
/**
 * 首页
 */

namespace Control;

use Moonrise\Core\Controller;
use Moonrise\Core\Loader;

class Index extends Controller
{
    public function index()
    {
        $b = $this->request->get('name', MR_TYPE_DEFAULT, array('xss_clean'=>1));
        dump($b);
        $idxModel = Loader::loadModel('index');
        dump($idxModel->showData());
    }
}