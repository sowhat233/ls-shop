<?php

namespace App\Observers;

use App\Models\Order;
use App\Tasks\CloseOrder;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class OrderObserver
{

    /**
     * 创建订单时，投递CloseOrder异步的任务队列
     * @param Order $order
     */
    public function created(Order $order)
    {

        $task = new CloseOrder($order);

        // 延迟投递任务 把订单关闭时间传入即可
        $task->delay(config('order.close_order_ttl'));

        // 出现异常时，累计尝试3次
        $task->setTries(3);

        // 判断是否投递成功
        if (Task::deliver($task)) {
            //
        }

    }


    /**
     * @param Order $order
     */
    public function updated(Order $order)
    {
        //
    }


    /**
     * @param Order $order
     */
    public function deleted(Order $order)
    {
        //
    }


    /**
     * @param Order $order
     */
    public function restored(Order $order)
    {
        //
    }


    /**
     * @param Order $order
     */
    public function forceDeleted(Order $order)
    {
        //
    }

}