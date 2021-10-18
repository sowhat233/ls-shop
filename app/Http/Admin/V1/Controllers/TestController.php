<?php


namespace App\Http\Admin\V1\Controllers;


use App\Tasks\TestTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class TestController
{


    public function test()
    {

        $task = new TestTask();
        Task::deliver($task);
        return 1;

    }

}