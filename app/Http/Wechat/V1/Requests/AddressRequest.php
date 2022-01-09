<?php


namespace App\Http\Wechat\V1\Requests;


use App\Http\Base\BaseFormRequest;

class AddressRequest extends BaseFormRequest
{

    public function rules()
    {

        $rules = [

            'city'     => ['bail', 'required', 'min:9', 'max:255'],
            'nickname' => ['bail', 'required', 'min:2', 'max:255'],
            'phone'    => ['bail', 'required', 'phone'],
            'address'  => ['bail', 'max:255'],

        ];

        return $rules;
    }


    public function messages()
    {

        $message = [

            'city.required'     => '城市未填写!',
            'city.min'          => '城市格式不正确!',
            'city.max'          => '城市最大不能超过255字符!',
            'nickname.required' => '名字未填写!',
            'nickname.min'      => '名字不能小于2个字!',
            'nickname.max'      => '名字最大不能超过255字符!',
            'phone.required'    => '手机号码未填写!',
            'phone.phone'       => '手机号码格式不正确!',
            'address.required'  => '地址最大不能超过255字符!',

        ];

        return $message;
    }

}