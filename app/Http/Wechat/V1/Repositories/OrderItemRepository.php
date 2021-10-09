<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Base\BaseRepository;
use App\Http\Common\CommonException;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\OrderItem;

class OrderItemRepository extends BaseRepository implements NotFoundExceptionInterface
{

    protected $model;


    /**
     * OrderItemRepository constructor.
     * @param OrderItem $orderItem
     */
    public function __construct(OrderItem $orderItem)
    {
        $this->model = $orderItem;
    }


    /**
     * @return mixed|void
     * @throws CommonException
     */
    public function notFoundException()
    {
        throw new CommonException('该订单下的商品不存在!', $this->httpNotFound);
    }


    /**
     * @param $order_id
     * @param $select
     * @return mixed
     */
    public function getOrderProductByOrderId($order_id, $select)
    {
        return $this->model->select($select)->where(compact('order_id'))->get();
    }

}
