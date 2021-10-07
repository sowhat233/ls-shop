<?php

namespace App\Http\Admin\V1\Repositories;

use App\Http\Admin\V1\Exceptions\ProductException;
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


    /**
     * @return mixed|void
     * @throws ProductException
     */
    public function notFoundException()
    {
        throw new ProductException('该商品不存在!', $this->httpNotFound);
    }


    /**
     * @param array $where
     * @param array $column
     * @param string $order
     * @param string $sort
     * @return mixed
     */
    public function getProductPaginate($where = [], $column = ['*'], $order = 'id', $sort = 'desc')
    {

        $product_list = $this->model->where($where)
                                    ->orderBy($order, $sort)
                                    ->with([
                                        'category' => function ($query) {

                                            return $query->select('id', 'name as category_name');

                                        },
                                        'sku'      => function ($query) {

                                            return $query->select('id', 'product_id', 'attrs', 'sales', 'stock', 'price');

                                        },

                                    ])
                                    ->select($column)
                                    ->paginate();

        return $product_list;
    }


    /**
     * @param $id
     * @param array $column
     * @param array $with
     * @return ProductRepository|ProductRepository[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findProductById($id, $column = ['*'], $with = [])
    {
        return $this->findOneOrFail($id, $this, $column, $with);
    }


    /**
     * @param $product_id
     * @param $product_column
     * @param $sku_column
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getProductWithSkuById($product_id, $product_column, $sku_column)
    {

        return $this->findOneOrFail($product_id, $this, $product_column, [
            'sku' => function ($query) use ($sku_column) {
                $query->select($sku_column);
            },
        ]);

    }


    /**
     * @param $category_id
     * @return mixed
     */
    public function getProductIdByCategoryId($category_id)
    {
        return $this->findValue(['category_id' => $category_id]);
    }


}
