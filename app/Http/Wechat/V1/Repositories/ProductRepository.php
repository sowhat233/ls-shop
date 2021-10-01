<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Wechat\V1\Exceptions\ProductException;
use App\Http\Base\BaseRepository;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Product;

class ProductRepository extends BaseRepository implements NotFoundExceptionInterface
{

    protected $model;


    public function __construct(Product $product)
    {
        $this->model = $product;
    }


    public function scopeStatus()
    {
        $where[] = ['status' => 1];

        return $where;
    }


    /**
     * @return mixed|void
     * @throws ProductException
     */
    public function notFoundException()
    {
        throw new ProductException('该商品不存在!', $this->httpNotFound);
    }


    /**
     * @param $ids
     * @param $sku_ids
     * @return mixed
     */
    public function getProductsByIdsWithSku($ids, $sku_ids)
    {

        return $this->model->whereIn('id', $ids)
                           ->where($this->scopeStatus())
                           ->with([
                               'sku' => function ($query) use ($sku_ids) {
                                   $query->whereIn('id', $sku_ids);
                               },
                           ])
                           ->select(['id', 'stock', 'price', 'name'])
                           ->get();

    }


    /**
     * 减库存
     * @param $id
     * @param $amount
     * @return mixed
     */
    public function decreaseStock($id, $amount)
    {
        return $this->where('id', $id)->where($this->scopeStatus())->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

}
