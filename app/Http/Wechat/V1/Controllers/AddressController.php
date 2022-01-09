<?php


namespace App\Http\Wechat\V1\Controllers;


use App\Http\Controllers\ApiController;
use App\Http\Wechat\V1\Repositories\AddressRepository;
use App\Http\Wechat\V1\Requests\AddressRequest;
use App\Http\Wechat\V1\Services\AddressService;

class AddressController extends ApiController
{

    private $name = '地址';


    /**
     * @param AddressRepository $addressRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(AddressRepository $addressRepository)
    {
        return $this->responseAsSuccess($addressRepository->all(['id', 'city', 'address', 'nickname', 'phone'], 'last_time'), $this->combineMessage("{$this->name}请求"));
    }


    /**
     * @param AddressRepository $addressRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function last(AddressRepository $addressRepository)
    {
        return $this->responseAsSuccess($addressRepository->first([], ['id', 'city', 'address', 'nickname', 'phone'], 'last_time'), $this->combineMessage("{$this->name}请求"));
    }


    /**
     * @param $id
     * @param AddressRepository $addressRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id, AddressRepository $addressRepository)
    {
        return $this->responseAsSuccess($addressRepository->findAddressById($id));
    }


    /**
     * @param $id
     * @param AddressRequest $request
     * @param AddressRepository $addressRepository
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     */
    public function update($id, AddressRequest $request, AddressRepository $addressRepository)
    {

        $addressRepository->update($id,$request->only(['city', 'nickname', 'phone', 'address']));

        return $this->responseAsSuccess($addressRepository->findAddressById($id), $this->combineMessage("{$this->name}编辑"));

    }


    /**
     * @param AddressRequest $request
     * @param AddressService $addressService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     * @throws \App\Http\Wechat\V1\Exceptions\TokenException
     */
    public function store(AddressRequest $request, AddressService $addressService)
    {
        return $this->responseAsCreated($addressService->store($request->only(['city', 'nickname', 'phone', 'address'])), $this->combineMessage("{$this->name}创建"));
    }

}