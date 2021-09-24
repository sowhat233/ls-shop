<?php


namespace App\Http\Wechat\V1\Services;


use App\Http\Wechat\V1\Exceptions\OrderException;
use App\Http\Wechat\V1\Repositories\OrderRepository;
use App\Http\Wechat\V1\Repositories\ProductRepository;
use DB;

class OrderService
{

    private $productRepo;

    private $orderRepo;

    public function __construct(ProductRepository $productRepo, OrderRepository $orderRepo)
    {
        $this->productRepo = $productRepo;
        $this->orderRepo   = $orderRepo;
    }


    /**
     * @param $params
     * @throws \Throwable
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
     * 根据前台提交的订单里的id获取商品
     * @param $selected_products
     * @return mixed
     */
    public function getProductsByOrder($selected_products)
    {

        $product_ids = $this->getSelectedProductId($selected_products);

        $products = $this->productRepo->getProductsByIdsWithSku($product_ids);

        return $this->ProductFlip($products);

    }


    /**
     * 把product的id当做当做新数组的key
     * @param $products
     * @return array
     */
    public function ProductFlip($products)
    {

        $product_arr = [];

        foreach ($products as $key => $value) {

            $product_arr[$value['id']] = $value;

        }

        return $product_arr;

    }


    /**
     * 检查库存 减库存 生成订单号
     * @param $selected_products
     * @param $products
     * @throws \Throwable
     */
    public function handlePlaceOrder($selected_products, $products)
    {

        //开启事务
        DB::beginTransaction();

        try {

            foreach ($selected_products as $key => $value) {

                //直接取下标
                if (isset($products[$value['id']])) {

                    $this_product = $products[$value['id']];

                    //检查库存 如果订单的库存大于数据库的库存 抛出异常
                    if ($value['amount'] > $this_product['stock']) {

                        $this->stockInsufficiency($this_product);
                    }

                    $status = $this->productRepo->decreaseStock($value['id'], $value['amount']);

                    //更新失败 说明库存不足 抛出异常
                    if ($status <= 0) {

                        $this->stockInsufficiency($this_product);
                    }

                }
                else {

                    throw new OrderException('有商品不存在或已下架!');
                }


            }

            //生成订单
            $order_no = $this->getOrderNo();

            /*
                $order_no
                $user_id
                $total_price
                $total_count
                $address_id

             */

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


    /**
     * 生成订单流水号
     * @return string
     * @throws OrderException
     */
    private function getOrderNo()
    {

        //订单流水号前缀
        $prefix = date('YmdHis');

        // 生成6位随机数与流水号拼接 然后去数据库查询是否存在 不存在则直接return 存在则继续生成
        // 100 次循环都生成 在数据库里已存在的订单号 应该是不可能的
        for ($i = 0; $i < 100; $i++) {

            $order_no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // 判断是否已经存在 不存在则返回
            if ( !$this->orderRepo->orderNotExists($order_no)) {

                return $order_no;

            }

        }

        \Log::warning('100次循环都没法生成不冲突的订单！！！！');

        throw new OrderException('系统错误,请稍后重试!');

    }

}