<?php

namespace App\Listeners;

use App\Http\Admin\V1\Logic\FdLogic;
use Hhxsv5\LaravelS\Swoole\Task\Listener;

class WebsocketOrderNotice extends Listener
{

    protected $swoole;

    protected $fdLogic;

    public function __construct(FdLogic $fdLogic)
    {
        $this->fdLogic = $fdLogic;
        $this->swoole  = app('swoole');
    }


    public function handle()
    {

        //发送websocket消息给所有在线的后台用户
        foreach ($this->fdLogic->list() as $key => $fd) {
            logDebug('WebsocketOrderNotice--- ' . $fd);
            $this->swoole->push($fd, '有新的订单!');
        }

    }

}