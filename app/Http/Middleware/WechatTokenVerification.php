<?php

namespace App\Http\Middleware;
/**
 * 微信小程序token验证中间件
 */

use App\Http\Wechat\V1\Exceptions\TokenException;
use App\Http\Wechat\V1\Logic\TokenLogic;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Closure;


class WechatTokenVerification
{

    private $TokenLogic;

    public function __construct(TokenLogic $tokenLogic)
    {
        $this->TokenLogic = $tokenLogic;
    }


    public function handle(Request $request, Closure $next)
    {

        $header_token = $request->header('access_token');

        if (!$header_token) {

            //抛出422异常
            throw new TokenException('header请求头参数里没有token!', FoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->TokenLogic->getCacheToken($header_token);

        return $next($request);
    }


}