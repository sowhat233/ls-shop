<?php

/**
 * 打印到日志里 测试用
 */
if ( !function_exists('TestWriteLog')) {

    function TestWriteLog($data)
    {

        $log_file = fopen(storage_path('logs'.DIRECTORY_SEPARATOR.'test.log'), 'a+');

        if (is_array($data)) {

            foreach ($data as $key => $value) {

                fwrite($log_file, date('Y-m-d H:i:s').': '.$key.'---'.$value.PHP_EOL);
            }
        }
        else {

            fwrite($log_file, date('Y-m-d H:i:s').': '.$data.PHP_EOL);

        }

        fclose($log_file);

    }
}
/**
 * 随机生成字符串
 */
if ( !function_exists('getRandChar')) {

    function getRandChar($length)
    {

        $str = null;

        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $max    = strlen($strPol)-1;

        for ($i = 0; $i < $length; $i++) {

            $str .= $strPol[rand(0, $max)];
        }

        return $str;

    }

}

/**
 * 封装CURL
 */
if ( !function_exists('curlGet')) {

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
 * 组装token
 */
if ( !function_exists('transformToken')) {

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
if ( !function_exists('wechatLoginUrl')) {

    function wechatLoginUrl($code)
    {

        $url = sprintf(
            config('wechat.login_url')
            , config('wechat.app_id'), config('wechat.app_secret'), $code);


        return $url;

    }


}