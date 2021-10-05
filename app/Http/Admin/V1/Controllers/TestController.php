<?php


namespace App\Http\Admin\V1\Controllers;


class TestController
{


    public function test()
    {
        app('swoole')->push(79, 'Push data to fd#1 in Controller');
        var_dump('ok');
    }

}