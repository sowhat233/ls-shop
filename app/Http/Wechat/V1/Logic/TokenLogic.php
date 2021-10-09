<?php


namespace App\Http\Wechat\V1\Logic;


use App\Http\Common\CommonException;
use App\Http\Wechat\V1\Exceptions\TokenException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenLogic
{


    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * @return mixed
     * @throws CommonException
     * @throws TokenException
     */
    public function getUserId()
    {
        return $this->getToken('user_id');
    }


    /**
     * 获取token
     * @param bool $value
     * @return mixed
     * @throws CommonException
     * @throws TokenException
     */
    public function getToken($value = false)
    {

        $token = $this->getCacheToken();

        if (!$value) {

            if (array_key_exists($value, $token)) {

                return $token[$value];
            }
            else {

                throw new CommonException('获取的token变量不存在!');
            }

        }

        return $token;

    }


    /**
     * 删除token
     * @param $token
     */
    public static function deleteToken($token)
    {
        Cache::forget($token);
    }


    /**
     * @param bool $header_token
     * @return mixed
     * @throws TokenException
     */
    public function getCacheToken($header_token = false)
    {

        if (!$header_token) {

            $header_token = $this->request->header(config('wechat.token_name'));
        }

        $token = Cache::get($header_token, null);

        if (is_null($token)) {

            throw new TokenException('token不存在或已过期!');
        }

        return $token;

    }


}