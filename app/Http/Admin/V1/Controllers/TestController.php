<?php


namespace App\Http\Admin\V1\Controllers;


use Illuminate\Http\Request;

class TestController
{

    private $test;


    public function __construct(Request $request)
    {
        $this->test = $request->input('xxx');
    }


}