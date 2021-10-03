<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Wechat\V1\Exceptions\ProductException;
use App\Http\Base\BaseRepository;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Order;

class OrderRepository extends BaseRepository implements NotFoundExceptionInterface
{

    protected $model;


    /**
     * OrderRepository constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->model = $order;
    }


    /**
     * @return mixed|void
     * @throws ProductException
     */
    public function notFoundException()
    {
        throw new ProductException('该订单不存在!', $this->httpNotFound);
    }


    /**
     * @param $order_no
     * @return mixed
     */
    public function orderExists($order_no)
    {
        return $this->model->where('no', $order_no)->exists();
    }


}
