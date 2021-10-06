<?php


namespace App\Http\Admin\V1\Controllers;

use App\Events\OrderPaid;
use Hhxsv5\LaravelS\Swoole\Task\Event;


class TestController
{


    public function test()
    {
        $success = Event::fire(app(OrderPaid::class));
        var_dump($success);// 判断是否触发成功
    }

}