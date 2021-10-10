<?php


namespace App\Http\Admin\V1\Controllers;

use App\Events\OrderPaid;
use App\Http\Admin\V1\Logic\FdLogic;
use App\Http\Admin\V1\Repositories\ProductRepository;
use App\Http\Base\BaseException;
use App\Http\Common\CommonException;
use DB;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Swoole\Exception;

class TestController
{

    protected $swoole;

    protected $fdLogic;

    protected $server;

    protected $productRepo;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepo = $productRepository;
        $this->fdLogic     = app(FdLogic::class);
        $this->swoole      = app('swoole');
    }


    public function test()
    {

        try {
            $a = 1;
            //添加product数据
            $product = $this->productRepo->create($a);
        } catch (\Throwable $e) {
            $message = 'test';
            throw new CommonException($message, $e);
        }
    }

}