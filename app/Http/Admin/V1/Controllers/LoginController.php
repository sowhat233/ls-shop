<?php

namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Repositories\ProductRepository;
use App\Http\Admin\V1\Requests\LoginRequest;
use App\Http\Controllers\ApiController;
use App\Models\Product;

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
            'expires_in'   => auth('api')->factory()->getTTL()*60,
        ];

        return responseJson($data);

    }

}
