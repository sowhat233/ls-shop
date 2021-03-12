<?php

namespace App\Http\Middleware;

use App\Http\Wechat\V1\Exceptions\TokenException;
use App\Http\Wechat\V1\Services\TokenService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Closure;


class WechatToken
{

    public function handle(Request $request, TokenService $tokenService, Closure $next)
    {

        $header_token = $request->header('token');

        if ( !$header_token) {

            //抛出422异常
            throw new TokenException('header请求头参数里没有token!', FoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($tokenService->getCacheToken($header_token)) {

            //抛出401异常
            throw new TokenException('token不存在或已过期!');
        }

        return $next($request);
    }


}