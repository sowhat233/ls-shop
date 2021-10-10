<?php


namespace App\Http\Wechat\V1\Services;


use App\Enums\OrderEnums;
use App\Events\OrderPaid;
use App\Http\Base\BaseException;
use App\Http\Common\CommonException;
use App\Http\Wechat\V1\Logic\TokenLogic;
use App\Http\Wechat\V1\Repositories\AddressRepository;
use App\Http\Wechat\V1\Repositories\OrderItemRepository;
use App\Http\Wechat\V1\Repositories\OrderRepository;
use App\Http\Wechat\V1\Repositories\ProductRepository;
use App\Http\Wechat\V1\Repositories\SkuRepository;
use DB;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class OrderService
{

    private $productRepo;

    private $skuRepo;

    private $orderRepo;

    private $orderItemRepo;

    private $addressRepo;

    private $tokenLogic;


    public function __construct(ProductRepository $productRepo, SkuRepository $skuRepo,
                                OrderRepository $orderRepo, OrderItemRepository $orderItemRepo,
                                AddressRepository $addressRepo, TokenLogic $token)
    {
        $this->productRepo   = $productRepo;
        $this->skuRepo       = $skuRepo;
        $this->orderRepo     = $orderRepo;
        $this->orderItemRepo = $orderItemRepo;
        $this->addressRepo   = $addressRepo;
        $this->tokenLogic    = $token;
    }


    /**
     * @param $params
     * @return mixed
     * @throws \Throwable
     */
    public function store($params)
    {

        $product_list = $params['product_list'];

        $product_ids = array_unique(array_column($product_list, 'product_id'));

        $sku_ids = array_column($product_list, 'sku_id');

        //根据前台提交的订单里的id获取商品
        $products = $this->flipProduct($this->productRepo->getProductsByIdsWithSku($product_ids, $sku_ids));

        $order = $this->handleCreateOrder($params, $products);

        //发送websocket消息通知所有在线的后台用户 暂时先放这里
        Event::fire(app(OrderPaid::class));

        return $order;
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
     * @throws CommonException
     */
    private function getTotalAmount($selected_products, $products)
    {

        $total_amount = 0.00;

        foreach ($selected_products as $key => $item) {

            //直接取下标
            if (isset($products[$item['product_id']])) {

                //这个this_product 是从数据库中拿出来的数据
                $this_product = $products[$item['product_id']];

                //有sku_id 就是 多规格
                if (isset($item['sku_id'])) {

                    //判断这个sku是否存在
                    if (isset($this_product['sku'][$item['sku_id']])) {

                        //这个this_sku 是从数据库中拿出来的数据
                        $this_sku = $this_product['sku'][$item['sku_id']];

                        //更新失败 说明库存不足 抛出异常
                        if ($this->skuRepo->decreaseStock($this_sku['id'], $item['amount']) <= 0) {

                            $this->underStockException($this_product);
                        }

                        //计算金额
                        $total_amount += $this_sku['price'] * $item['amount'];
                    }
                    else {

                        $this->productNotFoundException();
                    }

                }//单规格情况
                else {

                    //更新失败 说明库存不足 抛出异常
                    if ($this->productRepo->decreaseStock($this_product['id'], $item['amount']) <= 0) {

                        $this->underStockException($this_product);
                    }

                    //计算金额
                    $total_amount += $this_product['price'] * $item['amount'];

                }


            }//下标不存在 说明商品不存在或已下架
            else {

                $this->productNotFoundException();
            }


        }

        return $total_amount;
    }


    /**
     * @param $address_id
     * @param $user_id
     * @return false|string
     */
    private function getJsonAddress($address_id, $user_id)
    {
        return json_encode($this->addressRepo->findUserAddressById($address_id, compact($user_id)), true);
    }


    /**
     * @param $params
     * @param $products
     * @return array
     * @throws CommonException
     * @throws \App\Http\Wechat\V1\Exceptions\TokenException
     */
    private function getOrderColumnData($params, $products)
    {

        $user_id = $this->tokenLogic->getUserId();
        $user_id = 1; //待处理 需要删
        $order   = [

            'user_id'        => $user_id,

            //获取订单流水号
            'order_no'       => $this->getOrderNo(),

            //获取订单金额 顺便检查库存 减库存
            'total_amount'   => $this->getTotalAmount($params['product_list'], $products),

            //json化的地址
            'address'        => $this->getJsonAddress($params['address_id'], $user_id),

            //订单备注
            'remark'         => is_null($params['remark']) ? '' : $params['remark'],

            //支付时间
            'paid_at'        => 0,

            // 支付类型 微信支付
            'payment_method' => OrderEnums::WeChatPay,

            //支付平台订单号
            'payment_no'     => 0,

            //退款状态 默认0 未退款

            'refund_status'   => OrderEnums::NotRefund,

            //退款单号 默认0
            'refund_no'       => 0,

            //订单状态 默认为 未支付
            'pay_status'      => OrderEnums::NotPay,

            //订单物流状态 默认为  未发货
            'delivery_status' => OrderEnums::NoShipped,

            'create_at' => time(),

        ];

        return $order;
    }


    /**
     * @param $selected_products
     * @param $products
     * @param $order_id
     * @return array
     */
    private function getOrderItemColumnData($selected_products, $products, $order_id)
    {

        $data = [];

        foreach ($selected_products as $key => $item) {

            $data[$key]['sku_id']      = isset($item['sku_id']) ? $item['sku_id'] : 0;
            $data[$key]['order_id']    = $order_id;
            $data[$key]['product_id']  = $item['product_id'];
            $data[$key]['price']       = $this->getProductPrice($products, $item);//单价
            $data[$key]['amount']      = $item['amount'];//数量
            $data[$key]['rating']      = 0;//用户评分 默认为0
            $data[$key]['review']      = '';//用户评价
            $data[$key]['reviewed_at'] = 0;//评价时间

        }

        return $data;

    }


    /**
     * @param $product_list
     * @param $item
     * @return mixed
     */
    private function getProductPrice($product_list, $item)
    {

        $this_product = $product_list[$item['product_id']];

        //如果是sku商品
        if (isset($item['sku_id'])) {

            return $this_product['sku'][$item['sku_id']]['price'];
        }
        else {

            return $this_product['price'];
        }

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

            //添加订单商品数据
            $this->orderItemRepo->insert($this->getOrderItemColumnData($params['product_list'], $products, $order->id));

            DB::commit();

            return $order;

        } catch (\Throwable $e) {

            DB::rollBack();

            if ($e instanceof BaseException) {

                $message = $e->getMessage();

            }
            else {

                $message = '订单创建失败!';

            }

            throw new CommonException($message, $e);

        }


    }


    /**
     * @throws CommonException
     */
    private function productNotFoundException()
    {

        throw new CommonException('有商品不存在或已下架!');
    }


    /**
     * @param $product
     * @throws CommonException
     */
    private function underStockException($product)
    {
        throw new CommonException($product['name'] . '的库存不足!');
    }


    /**
     * 生成订单流水号
     * @return string
     * @throws CommonException
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

        \Log::warning('10次循环都没法生成不冲突的订单！！！');

        throw new CommonException('系统错误,请稍后重试!');

    }

}