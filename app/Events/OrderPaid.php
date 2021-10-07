<?php

namespace App\Events;

use App\Listeners\WebsocketOrderNotice;
use Hhxsv5\LaravelS\Swoole\Task\Event;

/**
 * 订单支付触发事件
 * Class OrderPaid
 * @package App\Events
 */
class OrderPaid extends Event
{

    protected $listeners = [
        // 监听器列表
        WebsocketOrderNotice::class,
    ];

}