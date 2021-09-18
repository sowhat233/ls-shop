<?php


namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Requests\TestRequest;

class TestController
{
    public function test(TestRequest $request)
    {
        return $request->all();
    }
}