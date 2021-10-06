<?php


namespace App\Http\Admin\V1\Logic;

use Illuminate\Support\Facades\Redis;

class FdLogic
{

    private $key = 'ws_fd';

    private $redis;


    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }


    /**
     * @return mixed
     */
    public function list()
    {
        return $this->redis::hkeys($this->key);
    }


    /**
     * @param $fd
     * @param $uid
     */
    public function add($fd, $uid)
    {
        $this->redis::hsetnx($this->key, $fd, $uid);
    }


    /**
     * @param $fd
     */
    public function del($fd)
    {
        $this->redis::hdel($this->key, $fd);
    }

}