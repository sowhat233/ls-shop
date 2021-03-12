<?php


namespace App\Http\Wechat\V1\Services;

use App\Http\Wechat\V1\Repositories\UserRepository;
use App\Http\Common\CommonException;
use App\Http\Wechat\V1\Exceptions\TokenException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenService
{

    private $userRepo;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }


    /**
     * @param $code
     * @return bool
     * @throws CommonException
     */
    public function handleToken($code)
    {

        //传递code 换取openid和session_key
        $wechat_result = $this->getWechatResult($code);

        //查询数据库 openid不存在就存入数据里 然后拿到对应的uid
        $uid = $this->userRepo->findUidByOpenId($wechat_result['openid']);

        $user_data = $this->transformUserData($wechat_result, $uid);

        //颁发token
        return $this->grantToken($user_data);

    }


    /**
     * @param $code
     * @return mixed
     * @throws CommonException
     */
    private function getWechatResult($code)
    {

        $result = json_decode(curlGet(wechatLoginUrl($code)), true);


        if (empty($result)) {

            throw new CommonException('获取session_key和openid时异常');

        }
        else {

            if (array_key_exists('errcode', $result)) {

                $message = 'errcode：'.$result['errcode'].';errmsg：'.$result['errmsg'];

                throw new CommonException($message);

            }

        }

        return $result;

    }


    /**
     * @param $wechat_data
     * @param $uid
     * @return mixed
     */
    private function transformUserData($wechat_data, $uid)
    {

        $wechat_data['uid'] = $uid;

        return $wechat_data;

    }


    /**
     * 颁发token
     * @param $user_data
     * @return bool
     */
    private function grantToken($user_data)
    {

        $token = $this->generateToken();

        $this->setToken($token, $user_data);

        return $token;

    }


    /**
     * 生成token
     * @return string
     */
    public function generateToken()
    {

        $rand_char = getRandChar(32);

        $timestamp = md5(time());

        $token_salt = config('wechat.token_salt');

        return sha1($rand_char.$timestamp.$token_salt);
    }


    /**
     * 把token和user_data绑定起来
     * @param $token
     * @param $user_data
     */
    public function setToken($token, $user_data)
    {
        Cache::put($token, $user_data, config('wechat.ttl'));
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

        if ($token === null) {

            throw new TokenException('token不存在或已过期!');
        }


        if ( !$value) {

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
     * @param bool $header_token
     * @return mixed
     */
    public function getCacheToken($header_token = false)
    {

        if ( !$header_token) {

            $header_token = Request::header(config('wechat.token_key'));
        }

        $token = Cache::get($header_token, null);

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
}