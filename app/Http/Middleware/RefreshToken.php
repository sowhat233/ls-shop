<?php

namespace App\Http\Middleware;

use App\Http\Admin\V1\Exceptions\TokenException;
use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


class RefreshToken extends BaseMiddleware
{


    public function handle($request, Closure $next)
    {

        // 检查此次请求中是否带有 token，如果没有则抛出异常。
        $this->checkForToken($request);

        //捕捉 token 过期所抛出的 TokenExpiredException 异常 (待处理 此处要改成刷新token)
        try {

            if ($this->auth->parseToken()->authenticate()) {

                return $next($request);
            }

            throw new TokenException('您还没有登录');

        } catch (TokenExpiredException $exception) {

            //token已过期也要抛403吗？此处存疑
            throw new TokenException('token已过期');

        }

    }


}