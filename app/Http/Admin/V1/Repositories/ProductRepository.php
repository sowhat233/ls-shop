<?php

namespace App\Http\Admin\V1\Repositories;

use App\Http\Admin\V1\Exceptions\ProductException;
use App\Http\Base\BaseRepository;
use App\Interfaces\notFoundExceptionInterface;
use App\Models\Product;

class ProductRepository extends BaseRepository implements notFoundExceptionInterface
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
        throw new ProductException('该商品不存在!', $this->not_found_code);
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
     * @param $category_id
     * @param null $value
     * @return mixed
     * @throws \App\Http\Common\CommonException
     */
    public function dissociateCategory($category_id, $value = null)
    {
        return $this->update($category_id, $value, 'category_id');
    }


}
