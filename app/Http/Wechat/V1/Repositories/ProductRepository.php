<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Common\CommonException;
use App\Http\Base\BaseRepository;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Product;

class ProductRepository extends BaseRepository implements NotFoundExceptionInterface
{

    protected $model;


    /**
     * ProductRepository constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->model = $product;
    }


    /**
     * @return mixed|void
     * @throws CommonException
     */
    public function notFoundException()
    {
        throw new CommonException('该商品不存在或已下架!', null, $this->httpNotFound);
    }


    /**
     * @param $ids
     * @param $sku_ids
     * @return mixed
     */
    public function getProductsByIdsWithSku($ids, $sku_ids)
    {

        return $this->model->whereIn('id', $ids)
                           ->where($this->model->scopeLaunched())
                           ->with([
                               'sku' => function ($query) use ($sku_ids) {
                                   $query->whereIn('id', $sku_ids);
                               },
                           ])
                           ->select(['id', 'stock', 'price', 'name'])
                           ->get();

    }


}
