<?php

namespace App\Events;

use App\Listeners\WebsocketOrderNotice;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class OrderPaid extends Event
{

    protected $listeners = [
        // 监听器列表
        WebsocketOrderNotice::class,
    ];

}