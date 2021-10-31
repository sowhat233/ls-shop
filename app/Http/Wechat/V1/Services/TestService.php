<?php


namespace App\Http\Wechat\V1\Services;


class TestService
{


    public function test($test)
    {
        logDebug($test());
    }
}