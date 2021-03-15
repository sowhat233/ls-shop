<?php


namespace App\Http\Wechat\V1\Controllers;


use App\Http\Wechat\V1\Requests\PlaceOrderRequest;
use App\Http\Wechat\V1\Services\OrderService;

class OrderController
{


    public function placeOrder(PlaceOrderRequest $request, OrderService $orderService)
    {

        $orderService->placeOrder($request->only(['address_id', 'product_list']));

        return responseJsonAsCreated('订单创建成功！');

    }
}