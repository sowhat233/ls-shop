<?php


namespace App\Http\Admin\V1\Logic;


use Tymon\JWTAuth\JWTAuth;

class TokenLogic
{

    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * @param $token
     * @return mixed
     */
    public function getUidByToken($token)
    {

        $this->auth->setToken($token);

        $user = $this->auth->authenticate();

        return $user->id;
    }

}