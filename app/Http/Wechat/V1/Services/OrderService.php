<?php


namespace App\Http\Wechat\V1\Services;


use App\Http\Wechat\V1\Exceptions\OrderException;
use App\Http\Wechat\V1\Repositories\AddressRepository;
use App\Http\Wechat\V1\Repositories\OrderRepository;
use App\Http\Wechat\V1\Repositories\ProductRepository;
use App\Http\Wechat\V1\Repositories\SkuRepository;
use DB;

class OrderService
{

    private $productRepo;

    private $skuRepo;

    private $orderRepo;

    private $addressRepo;


    public function __construct(ProductRepository $productRepo, SkuRepository $skuRepo,
                                OrderRepository $orderRepo, AddressRepository $addressRepo)
    {
        $this->productRepo = $productRepo;
        $this->skuRepo     = $skuRepo;
        $this->orderRepo   = $orderRepo;
        $this->addressRepo = $addressRepo;
    }


    /**
     * @param $params
     * @throws \Throwable
     */
    public function store($params)
    {

        $product_list = $params['product_list'];

        $product_ids = array_column($product_list, 'id');

        $sku_ids = $this->getSkuIdByProductList($product_list);

        //根据前台提交的订单里的id获取商品
        $products = $this->flipProduct($this->productRepo->getProductsByIdsWithSku($product_ids, $sku_ids));

        $this->handleCreateOrder($params['product_list'], $products, $params['address_id']);

    }


    /**
     * @param $product_list
     * @return array
     */
    private function getSkuIdByProductList($product_list)
    {

        $sku_ids = [];

        foreach ($product_list as $key => $value) {

            array_merge($sku_ids, array_column($value, 'sku_id'));
        }

        return $sku_ids;
    }


    /**
     * 把product的id当做当做新数组的key
     * @param $products
     * @return array
     */
    public function flipProduct($products)
    {

        $product_arr = [];

        foreach ($products as $key => $item) {

            $product_arr[$item['id']] = $item;

            //翻转key
            foreach ($item['sku'] as $k => $sku) {

                $product_arr[$item['id']]['sku'][$sku['id']] = $sku;

            }

        }

        return $product_arr;

    }


    /**
     * 检查库存并计算订单金额
     * @param $selected_products
     * @param $products
     * @return float|int
     * @throws OrderException
     */
    public function getTotalAmount($selected_products, $products)
    {

        $total_amount = 0.00;

        foreach ($selected_products as $key => $item) {

            //直接取下标
            if (isset($products[$item['id']])) {

                $this_product = $products[$item['id']];

                //单规格情况
                if (count($item['sku_list']) === 0) {

                    //检查库存 如果订单的下单量大于数据库的库存 抛出异常
                    if ($item['amount'] > $this_product['stock']) {

                        $this->underStockException($this_product);
                    }

                    //更新失败 说明库存不足 抛出异常
                    if ($this->productRepo->decreaseStock($item['id'], $item['amount']) <= 0) {

                        $this->underStockException($this_product);
                    }

                    //计算金额
                    $total_amount += $this_product['price'] * $item['amount'];

                }//多规格情况
                else {

                    //检查每一个sku的库存
                    foreach ($item['sku_list'] as $k => $sku) {

                        if (isset($this_product['sku'][$sku['id']])) {

                            $this_sku = $this_product['sku'][$sku['id']];

                            //如果订单的下单量大于数据库的库存 抛出异常
                            if ($sku['amount'] > $this_sku['stock']) {

                                $this->underStockException($this_product);
                            }

                            //更新失败 说明库存不足 抛出异常
                            if ($this->skuRepo->decreaseStock($item['id'], $item['amount']) <= 0) {

                                $this->underStockException($this_product);
                            }

                            //计算金额
                            $total_amount += $this_sku['price'] * $sku['amount'];

                        }
                        else {

                            $this->productNotFoundException();
                        }


                    }


                }


            }
            else {

                $this->productNotFoundException();
            }


        }

        return $total_amount;
    }

    /**
     * @param $address_id
     * @return false|string
     */
    private function getJsonAddress($address_id)
    {
        return json_encode($this->addressRepo->findUserAddressById(1, $address_id), true);
    }


    /**
     * 检查库存 减库存 生成订单号
     * @param $selected_products
     * @param $products
     * @param $address_id
     * @throws \Throwable
     */
    public function handleCreateOrder($selected_products, $products, $address_id)
    {

        //开启事务
        DB::beginTransaction();

        try {

            //获取订单金额 顺便检查库存
            $total_amount = $this->getTotalAmount($selected_products, $products);

            //获取订单流水号
            $order_no = $this->getOrderNo();

            $address = $this->getJsonAddress($address_id);


            /*
                $total_amount
                $order_no
                $address
                $user_id


             */

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

        }


    }


    /**
     * @throws OrderException
     */
    private function productNotFoundException()
    {

        throw new OrderException('有商品不存在或已下架!');
    }


    /**
     * @param $product
     * @throws OrderException
     */
    private function underStockException($product)
    {

        throw new OrderException($product['name'] . '的库存不足!');
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
        // 10 次循环都生成 在数据库里已存在的订单号
        for ($i = 0; $i < 10; $i++) {

            $order_no = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // 判断是否已经存在 不存在则返回
            if (!$this->orderRepo->orderExists($order_no)) {

                return $order_no;

            }

        }

        \Log::warning('10次循环都没法生成不冲突的订单！！！！');

        throw new OrderException('系统错误,请稍后重试!');

    }

}