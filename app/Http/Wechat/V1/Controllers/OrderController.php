<?php


namespace App\Http\Wechat\V1\Controllers;


use App\Http\Controllers\ApiController;
use App\Http\Wechat\V1\Repositories\OrderRepository;
use App\Http\Wechat\V1\Requests\OrderRequest;
use App\Http\Wechat\V1\Services\OrderService;

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

        return $this->responseAsSuccess($orderService->store($request->only(['address_id', 'product_list'])), $this->combineMessage("{$this->name}创建"));

    }

}