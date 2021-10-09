<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Common\CommonException;
use App\Http\Base\BaseRepository;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\User;

class UserRepository extends BaseRepository implements NotFoundExceptionInterface
{

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }


    public function notFoundException()
    {
        throw new CommonException('该用户不存在!', $this->httpNotFound);
    }


    /**
     * @param $id
     * @param array $column
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findUserById($id, $column = ['*'], $with = [])
    {
        return $this->findOneOrFail($id, $this, $column, $with);
    }


    /**
     * 如果没有 就新增
     * @param $openid
     * @return mixed
     */
    public function findUidByOpenId($openid)
    {

        $uid = $this->model->where(compact('openid'))->value('id');

        if (is_null($uid)) {

            $uid = $this->insertGetId(['openid' => $openid]);

        }

        return $uid;

    }


}
