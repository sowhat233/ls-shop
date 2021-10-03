<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Base\BaseRepository;
use App\Http\Wechat\V1\Exceptions\AddressException;
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
     * @throws AddressException
     */
    public function notFoundException()
    {
        throw new AddressException('该地址不存在!', $this->httpNotFound);
    }


    /**
     * @param $address_id
     * @param $where
     * @return mixed
     */
    public function findUserAddressById( $address_id,$where)
    {
        return $this->findOneOrFail($address_id, $this, ['address', 'contact_phone', 'contact_name', 'zip'], [], $where);
    }


}
