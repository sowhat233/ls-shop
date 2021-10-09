<?php


namespace App\Http\Admin\V1\Controllers;

use App\Events\OrderPaid;
use App\Http\Admin\V1\Logic\FdLogic;
use App\Http\Admin\V1\Repositories\ProductRepository;
use App\Http\Base\BaseException;
use DB;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Swoole\Exception;

class TestController
{

    protected $swoole;

    protected $fdLogic;

    protected $server;

    public function __construct()
    {

        $this->fdLogic = app(FdLogic::class);
        $this->swoole  = app('swoole');
    }


    public function test()
    {

        foreach ($this->fdLogic->list() as $key => $fd) {

            if ($this->swoole->isEstablished($fd)) {

                $this->swoole->push($fd, '有新的订单!');
            }


        }

    }

}