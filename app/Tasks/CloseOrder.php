<?php


namespace App\Tasks;

use App\Http\Admin\V1\Repositories\ProductRepository;
use App\Http\Wechat\V1\Repositories\OrderItemRepository;
use App\Http\Wechat\V1\Repositories\OrderRepository;
use App\Http\Wechat\V1\Repositories\SkuRepository;
use DB;
use App\Enums\OrderEnums;
use Hhxsv5\LaravelS\Swoole\Task\Task;

/**
 * 关闭未支付的订单
 * Class CloseOrder
 * @package APP\Tasks3
 */
class CloseOrder extends Task
{

    private $order;

    private $productRep;

    private $skuRep;

    private $orderRep;

    private $orderItemRep;


    public function __construct($order)
    {
        $this->order        = $order;
        $this->productRep   = app(ProductRepository::class);
        $this->skuRep       = app(SkuRepository::class);
        $this->orderRep     = app(OrderRepository::class);
        $this->orderItemRep = app(OrderItemRepository::class);
    }


    // 处理任务的逻辑，运行在Task进程中，不能投递任务
    public function handle()
    {

        //如果已经支付则不需要关闭订单,直接退出
        if ($this->order->status == OrderEnums::orderPay) {
            return true;
        }

        //开启事务
        DB::beginTransaction();

        try {

            $order_id = $this->order->id;

            // 关闭订单 即修改订单的 pay_status 字段为 0
            $this->orderRep->update($order_id, ['pay_status' => OrderEnums::Closed]);

            // 把订单中商品的数量加回到库存中
            $this->releaseStock($order_id);

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error(__CLASS__ . '关闭订单并释放库存的task出错 order_id: ' . $this->order->id . "\n报错信息：" . $e->getMessage());

        }


    }


    /**
     * 把订单中商品的数量加回到库存中
     * @param $order_id
     * @throws \App\Http\Common\CommonException
     */
    public function releaseStock($order_id)
    {

        //找出所有的商品
        $products = $this->orderItemRep->getOrderProductByOrderId($order_id, ['product_id', 'sku_id', 'amount']);

        foreach ($products as $key => $item) {

            $sku_id = $item['sku_id'];
            $amount = $item['amount'];

            //单规格商品
            if ($sku_id == 0) {

                $this->productRep->incrementStock($item['product_id'], $amount);

            } //sku商品
            else {

                $this->skuRep->incrementStock($sku_id, $amount);
            }

        }

    }


    // 完成事件，任务处理完后的逻辑，运行在Worker进程中，可以投递任务
    public function finish()
    {
        //
    }

}