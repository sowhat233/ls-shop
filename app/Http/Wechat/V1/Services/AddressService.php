<?php


namespace App\Http\Wechat\V1\Services;


use App\Http\Wechat\V1\Logic\TokenLogic;
use App\Http\Wechat\V1\Repositories\AddressRepository;

class AddressService
{
    private $tokenLogic;
    private $addressRepository;

    public function __construct(TokenLogic $tokenLogic, AddressRepository $addressRepository)
    {
        $this->tokenLogic        = $tokenLogic;
        $this->addressRepository = $addressRepository;
    }


    /**
     * @param $params
     * @return mixed
     * @throws \App\Http\Common\CommonException
     * @throws \App\Http\Wechat\V1\Exceptions\TokenException
     */
    public function store($params)
    {

        $insert = [
            'user_id'       => $this->tokenLogic->getUserId(),
            'city'          => $params['city'],
            'address'       => $params['address'],
            'contact_name'  => $params['contact_name'],
            'contact_phone' => $params['contact_phone'],
            'last_time'     => time(),
        ];

        return $this->addressRepository->create($insert);

    }



}