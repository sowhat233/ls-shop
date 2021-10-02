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
        $user_id = $this->userRepo->findUidByOpenId($wechat_result['openid']);

        $user_data = $this->transformUserData($wechat_result, $user_id);

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

                $message = 'errcode：' . $result['errcode'] . ';errmsg：' . $result['errmsg'];

                throw new CommonException($message);

            }

        }

        return $result;

    }


    /**
     * @param $wechat_data
     * @param $user_id
     * @return mixed
     */
    private function transformUserData($wechat_data, $user_id)
    {

        $wechat_data['user_id'] = $user_id;

        return $wechat_data;

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

        return sha1($rand_char . $timestamp . $token_salt);
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
     * 把token和user_data绑定起来
     * @param $token
     * @param $user_data
     */
    public function setToken($token, $user_data)
    {
        Cache::put($token, $user_data, config('wechat.ttl'));
    }


}