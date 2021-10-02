<?php


namespace App\Http\Wechat\V1\Controllers;


use App\Http\Controllers\ApiController;
use App\Http\Wechat\V1\Requests\TokenRequest;
use App\Http\Wechat\V1\Services\TokenService;

class TokenController extends ApiController
{


    /**
     * @param TokenRequest $request
     * @param TokenService $tokenService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     */
    public function getToken(TokenRequest $request, TokenService $tokenService)
    {

        $token = $tokenService->handleToken($request->input('code'));

        return responseJson(transformToken($token, (time() + config('wechat.token_ttl')), config('wechat.token_name')));

    }


}