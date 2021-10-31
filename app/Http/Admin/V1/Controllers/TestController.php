<?php


namespace App\Http\Admin\V1\Controllers;


use App\Http\Controllers\ApiController;
use App\Tasks\TestTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class TestController extends ApiController
{

    public function test()
    {

        logDebug(1);
        return $this->responseAsSuccess('test');
    }
}