<?php


namespace App\Http\Wechat\V1\Services;


use App\Http\Wechat\V1\Exceptions\OrderException;
use App\Http\Wechat\V1\Repositories\ProductRepository;
use DB;

class OrderService
{

    private $productRepo;


    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }


    /**
     * @param $params
     */
    public function placeOrder($params)
    {

        $products = $this->getProductsByOrder($params['product_list']);


        $this->handlePlaceOrder($params['product_list'], $products);

    }

    /**
     * @param $selected_products
     * @return array
     */
    public function getSelectedProductId($selected_products)
    {

        $product_ids = [];

        foreach ($selected_products as $value) {
            array_push($product_ids, $value['id']);
        }

        return $product_ids;

    }


    /**
     * 根据订单里的id获取产品
     * @param $selected_products
     * @return mixed
     */
    public function getProductsByOrder($selected_products)
    {

        $product_ids = $this->getSelectedProductId($selected_products);

        return $this->productRepo->getProductsByIdsWithSku($product_ids);

    }


    /**
     * 检查库存 减库存 生成订单号
     * @param $selected_products
     * @param $products
     */
    public function handlePlaceOrder($selected_products, $products)
    {

        //开启事务
        DB::beginTransaction();

        try {

            foreach ($selected_products as $key => $value) {

                foreach ($products as $k => $v) {

                    if ($value['id'] == $v['id']) {

                        //检查库存 如果订单的库存大于数据库的库存 抛出异常
                        if ($value['amount'] > $v['stock']) {

                            $this->stockInsufficiency($v);
                        }

                        $status = $this->productRepo->decreaseStock($value['id'], $value['amount']);

                        //更新失败 说明库存不足 抛出异常
                        if ($status <= 0) {

                            $this->stockInsufficiency($v);
                        }

                    }

                }


            }

            // 待处理 差个生成订单


            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

        }


    }


    /**
     * @param $product
     * @throws OrderException
     */
    private function stockInsufficiency($product)
    {

        throw new OrderException($product['name'].'的库存不足!');
    }

}