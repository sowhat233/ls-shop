<?php


if (!function_exists('logDebug')) {

    /**
     * @param $message
     * @return string|null
     */
    function logDebug($message)
    {

        \Illuminate\Support\Facades\Log::debug($message);

    }

}

/**
 * 随机生成字符串
 */
if (!function_exists('getRandChar')) {

    /**
     * @param $length
     * @return string|null
     */
    function getRandChar($length)
    {

        $str = null;

        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $max    = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {

            $str .= $strPol[rand(0, $max)];
        }

        return $str;

    }

}

/**
 * 封装CURL
 */
if (!function_exists('curlGet')) {

    /**
     * @param $url
     * @param int $http_code
     * @return bool|string
     */
    function curlGet($url, &$http_code = 0)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $file_contents = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $file_contents;
    }

}

/**
 * 获取两个字符串之间的字符串
 */
if (!function_exists('getStrBetween')) {

    /**
     * @param $input
     * @param $start
     * @param $end
     * @return bool|string
     */
    function getStrBetween($input, $start, $end)
    {
        return substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
    }

}

/**
 * 组装token
 */
if (!function_exists('transformToken')) {

    /**
     * @param $token
     * @param $expires_in
     * @param string $token_name
     * @return array
     */
    function transformToken($token, $expires_in, $token_name = 'access_token')
    {

        $data = [
            $token_name  => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expires_in,
        ];

        return $data;
    }

}

/**
 * 组装微信login地址
 */
if (!function_exists('wechatLoginUrl')) {

    function wechatLoginUrl($code)
    {

        $url = sprintf(config('wechat.login_url'), config('wechat.app_id'), config('wechat.app_secret'), $code);


        return $url;

    }


}

