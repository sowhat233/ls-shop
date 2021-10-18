<?php


namespace App\Tasks;


use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Redis;


class TestTask extends Task
{


    public function handle()
    {


        try {

            while (true) {

                $data = Redis::lpop('tasks');

                //如果没有数据
                if (!$data) {

                    //暂停1秒
                    sleep(1);
                    logDebug('没数据');
                }
                else {

                    logDebug('有数据了！！');

                }

            }

        } catch (\Throwable $e) {


            \Log::error("死循环请求redis的task出错 \n报错信息：" . $e->getMessage());

        }


    }


    // 完成事件，任务处理完后的逻辑，运行在Worker进程中，可以投递任务
    public function finish()
    {
        //
    }

}