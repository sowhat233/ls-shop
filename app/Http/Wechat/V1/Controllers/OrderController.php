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
     * @param OrderRepository $orderRepo
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(OrderRequest $request, OrderService $orderService,OrderRepository $orderRepo)
    {

        return $this->responseAsSuccess($orderRepo->find($orderService->store($request->only(['address_id', 'product_list']))), $this->combineMessage("{$this->name}创建"));

    }
}