<?php


namespace App\Http\Wechat\V1\Services;


use App\Enums\OrderEnums;
use App\Http\Wechat\V1\Exceptions\OrderException;
use App\Http\Wechat\V1\Logic\TokenLogic;
use App\Http\Wechat\V1\Repositories\AddressRepository;
use App\Http\Wechat\V1\Repositories\OrderRepository;
use App\Http\Wechat\V1\Repositories\ProductRepository;
use App\Http\Wechat\V1\Repositories\SkuRepository;
use DB;
use Psy\Exception\FatalErrorException;

class OrderService
{

    private $productRepo;

    private $skuRepo;

    private $orderRepo;

    private $addressRepo;

    private $token;


    public function __construct(ProductRepository $productRepo, SkuRepository $skuRepo,
                                OrderRepository $orderRepo, AddressRepository $addressRepo, TokenLogic $token)
    {
        $this->productRepo = $productRepo;
        $this->skuRepo     = $skuRepo;
        $this->orderRepo   = $orderRepo;
        $this->addressRepo = $addressRepo;
        $this->token       = $token;
    }


    /**
     * @param $params
     * @return mixed
     * @throws \Throwable
     */
    public function store($params)
    {

        $product_list = $params['product_list'];

        $product_ids = array_column($product_list, 'id');

        $sku_ids = $this->getSkuIdByProductList($product_list);

        //根据前台提交的订单里的id获取商品
        $products = $this->flipProduct($this->productRepo->getProductsByIdsWithSku($product_ids, $sku_ids));

        return $this->handleCreateOrder($params, $products);

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
            if (isset($products[$item['product_id']])) {

                $this_product = $products[$item['product_id']];

                //单规格情况
                if (count($item['sku_list']) === 0) {

                    //更新失败 说明库存不足 抛出异常
                    if ($this->productRepo->decreaseStock($item['product_id'], $item['amount']) <= 0) {

                        $this->underStockException($this_product);
                    }

                    //计算金额
                    $total_amount += $this_product['price'] * $item['amount'];

                }//多规格情况
                else {

                    //检查每一个sku的库存
                    foreach ($item['sku_list'] as $k => $sku) {

                        if (isset($this_product['sku'][$sku['sku_id']])) {

                            $this_sku = $this_product['sku'][$sku['sku_id']];

                            //更新失败 说明库存不足 抛出异常
                            if ($this->skuRepo->decreaseStock($sku['sku_id'], $item['amount']) <= 0) {

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
     * @param $params
     * @param $products
     * @return array
     * @throws OrderException
     * @throws \App\Http\Common\CommonException
     * @throws \App\Http\Wechat\V1\Exceptions\TokenException
     */
    private function getOrderColumnData($params, $products)
    {

        return [

            'user_id'         => $this->token->getUserId(),

            //获取订单流水号
            'order_no'        => $this->getOrderNo(),

            //获取订单金额 顺便检查库存 减库存
            'total_amount'    => $this->getTotalAmount($params['selected_products'], $products),

            //json化的地址
            'address'         => $this->getJsonAddress($params['address_id']),

            //订单备注
            'remark'          => $params['remark'],

            //支付时间
            'paid_at'         => 0,

            // 支付类型 微信支付
            'paryment_method' => OrderEnums::WeChatPay,

            //支付平台订单号
            'payment_no'      => 0,

            //退款状态 默认0 未退款

            'refund_status'   => OrderEnums::NotRefund,

            //退款单号 默认0
            'refund_no'       => 0,

            //订单状态 默认为 正常
            'is_closed'       => OrderEnums::NotClosed,

            //订单是否已评价 默认为 未评价
            'is_reviewed'     => OrderEnums::NotReviewed,

            //订单物流状态 默认为  未发货
            'delivery_status' => OrderEnums::NoShipped,

            'create_at' => time(),

        ];

    }


    /**
     * @param $products
     * @param $order_id
     * @return array
     */
    private function getOrderItemColumnData($products, $order_id)
    {

        $data = [];

        foreach ($products as $key => $item) {

            //单规格情况
            if (count($item['sku_list']) === 0) {

                $data[]['order_id']   = $order_id;
                $data[]['product_id'] = $item['product_id'];
                $data[]['amount']     = $item['amount'];
                $data[]['price']      = $item['price'];

            }//多规格情况
            else {

                foreach ($item['sku_list'] as $k => $sku) {

                    $data[]['order_id']   = $order_id;
                    $data[]['product_id'] = $item['product_id'];
                    $data[]['sku_id']     = $sku['sku_id'];
                    $data[]['amount']     = $sku['amount'];
                    $data[]['price']      = $sku['price'];

                }
            }
        }


        return $data;
    }


    /**
     * @param $params
     * @param $products
     * @return mixed
     * @throws \Throwable
     */
    public function handleCreateOrder($params, $products)
    {

        //开启事务
        DB::beginTransaction();

        try {

            //添加订单数据
            $order = $this->orderRepo->create($this->getOrderColumnData($params, $products));

            $this->skuRepo->create($this->getOrderItemColumnData($params['selected_products'], $order->id));

            DB::commit();

            return $order;

        } catch (\Exception $e) {

            DB::rollBack();

            if ($e instanceof FatalErrorException) {

                $message = '订单创建失败!';

            }
            else {

                $message = $e->getMessage();
            }

            throw new OrderException($message);

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