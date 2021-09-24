<?php


namespace App\Http\Admin\V1\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;

class TestController
{
    public function test(Request $request)
    {

        return ['test' => Product::get()];
    }
}