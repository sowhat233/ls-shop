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


    public function test()
    {
        return '14.08.d1ddd';
    }

}