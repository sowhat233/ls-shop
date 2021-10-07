<?php


namespace App\Http\Wechat\V1\Controllers;


use App\Http\Controllers\ApiController;
use App\Http\Wechat\V1\Requests\TokenRequest;
use App\Http\Wechat\V1\Services\TokenService;

class TokenController extends ApiController
{

    private $name = '令牌';


    /**
     * @param TokenRequest $request
     * @param TokenService $tokenService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     */
    public function store(TokenRequest $request, TokenService $tokenService)
    {

        $token = $tokenService->handleToken($request->input('code'));

        return $this->responseAsCreated(transformToken($token, (time() + config('wechat.token_ttl')), $this->combineMessage("{$this->name}创建")));

    }


}