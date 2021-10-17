<?php

namespace App\Listeners;

use App\Http\Admin\V1\Logic\FdLogic;
use Hhxsv5\LaravelS\Swoole\Task\Listener;
use Swoole\Exception;

/**
 * 用户支付订单后,发送websocket消息通知所有在线的后台用户
 * Class WebsocketOrderNotice
 * @package App\Listeners
 */
class WebsocketOrderNotice extends Listener
{

    protected $swoole;

    protected $fdLogic;


    public function __construct()
    {

        $this->fdLogic = app(FdLogic::class);
        $this->swoole  = app('swoole');
    }


    public function handle()
    {

        foreach ($this->fdLogic->list() as $key => $fd) {

            if ($this->swoole->isEstablished($fd)) {

                $this->swoole->push($fd, '有新的订单!');
            }
            else {

                //删掉存在redis但并未连接的fd
                $this->fdLogic->del($fd);
            }


        }

    }

}