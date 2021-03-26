<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Wechat\V1\Exceptions\ProductException;
use App\Http\Base\BaseRepository;
use App\Interfaces\notFoundExceptionInterface;
use App\Models\Order;

class OrderRepository extends BaseRepository implements notFoundExceptionInterface
{

    protected $model;

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
        throw new ProductException('该订单不存在!', $this->not_found_code);
    }


    /**
     * @param $order_no
     * @return mixed
     */
    public function orderNoExists($order_no)
    {
        return $this->where('no', $order_no)->exists();
    }


}
