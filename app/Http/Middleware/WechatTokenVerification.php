<?php

namespace App\Http\Middleware;
/**
 * 微信小程序token验证中间件
 */

use App\Http\Wechat\V1\Exceptions\TokenException;
use App\Http\Wechat\V1\Services\TokenService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Closure;


class WechatTokenVerification
{

    private $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }


    public function handle(Request $request, Closure $next)
    {

        $header_token = $request->header('access_token');

        if ( !$header_token) {

            //抛出422异常
            throw new TokenException('header请求头参数里没有token!', FoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($this->tokenService->getCacheToken($header_token) === null) {

            //抛出401异常
            throw new TokenException('token不存在或已过期!');
        }

        return $next($request);
    }


}