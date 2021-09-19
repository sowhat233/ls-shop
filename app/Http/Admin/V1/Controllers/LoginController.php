<?php

namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Requests\LoginRequest;
use App\Http\Controllers\ApiController;

class LoginController extends ApiController
{

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LoginRequest $request)
    {

        $credentials = $request->only(['username', 'password']);

        if ( !$token = \Auth::guard('admin')->attempt($credentials)) {

            return $this->failed('账号或密码错误!');

        }

        $data = [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => config('jwt.ttl')*60,//jwt以分钟为单位 这里乘以60 使其变成秒单位
        ];

        return $this->responseAsSuccess($this->constituteMessage("登录"), $data);

    }

}
