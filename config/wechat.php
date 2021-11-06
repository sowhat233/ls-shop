<?php

return [

    'token_salt' => '4gJJh3Mo5DgzIhtkQBZcSgTwjGPDFeIL',
    'token_ttl'  => 86400*365,
    'login_url'  => "https://api.weixin.qq.com/sns/jscode2session?".
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
    'app_id'     => env('APP_ID'),
    'app_secret' => env('APP_SECRET'),

];
