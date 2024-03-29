<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Base\BaseRepository;
use App\Http\Common\CommonException;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Address;

class AddressRepository extends BaseRepository implements NotFoundExceptionInterface
{

    protected $model;

    /**
     * AddressRepository constructor.
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $this->model = $address;
    }


    /**
     * @return mixed|void
     * @throws CommonException
     */
    public function notFoundException()
    {
        throw new CommonException('地址不存在!', null, $this->httpNotFound);
    }


    /**
     * @param $id
     * @param array $where
     * @return mixed
     */
    public function findAddressById($id, $where = [])
    {
        return $this->findOneOrFail($id, $this, ['city', 'address', 'phone', 'nickname'], [], $where);
    }


}
