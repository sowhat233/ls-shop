<?php


namespace App\Http\Wechat\V1\Controllers;


use App\Enums\OrderEnums;
use App\Events\OrderPaid;
use App\Http\Controllers\ApiController;
use App\Http\Wechat\V1\Repositories\OrderRepository;
use App\Http\Wechat\V1\Requests\OrderRequest;
use App\Http\Wechat\V1\Services\OrderService;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class OrderController extends ApiController
{

    private $name = '订单';


    /**
     * @param OrderRequest $request
     * @param OrderService $orderService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(OrderRequest $request, OrderService $orderService)
    {

        return $this->responseAsCreated($orderService->store($request->only(['address_id', 'product_list', 'remark'])), $this->combineMessage("{$this->name}创建"));

    }


    /**
     * @param $order_id
     * @param OrderRepository $repository
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     */
    public function pay($order_id, OrderRepository $repository)
    {

        // 修改订单成功
        $repository->update($order_id, ['pay_status' => OrderEnums::orderPay]);

        //发送websocket消息通知所有在线的后台用户
        Event::fire(app(OrderPaid::class));

        return $this->responseAsSuccess([], '支付成功!');
    }

}