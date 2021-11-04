<?php


namespace App\Http\Wechat\V1\Controllers;


use App\Events\OrderPaid;
use App\Http\Controllers\ApiController;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class PayController extends ApiController
{

    private $name = '订单支付';

    //假装这个是支付成功的接口
    public function test()
    {

        Event::fire(app(OrderPaid::class));

        //发送websocket消息通知所有在线的后台用户 暂时先放这里
        return $this->responseAsSuccess([], $this->combineMessage("{$this->name}成功"));

    }
}