<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Base\BaseRepository;
use App\Http\Wechat\V1\Exceptions\SkuException;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Product;

class SkuRepository extends BaseRepository implements NotFoundExceptionInterface
{

    protected $model;


    public function __construct(Product $product)
    {
        $this->model = $product;
    }


    /**
     * @return mixed|void
     * @throws SkuException
     */
    public function notFoundException()
    {
        throw new SkuException('该sku不存在!', $this->httpNotFound);
    }


    /**
     * 减库存
     * @param $id
     * @param $amount
     * @return mixed
     */
    public function decreaseStock($id, $amount)
    {
        return $this->where('id', $id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

}
