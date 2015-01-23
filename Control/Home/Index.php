<?php

namespace Control\Home;

use Moonrise\Core\MController;

class Index extends MController
{
    public function index($para)
    {
        dump($para);
    }
}