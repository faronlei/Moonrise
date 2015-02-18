<?php

namespace Model;

use Moonrise\Core\Model;

class Index extends Model
{
    protected $service = 'default';

    public function showData()
    {
        $db = $this->connectDB();
        $db = $this->connectDB();
        //$db->begin_transaction();
        $res = $db->query('select * from user');
        $a = $res->fetch_all();

        $db->select_db('sunrise');

        $res1 = $db->query('select * from user');

        dump($res1->fetch_all());



        //$db->commit();
        return $a;
    }
}