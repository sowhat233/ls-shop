<?php


namespace App\Http\Wechat\V1\Requests;


use App\Http\Base\BaseFormRequest;

class TokenRequest extends BaseFormRequest
{


    public function rules()
    {
        $rules = [
            'code' => 'required',
        ];

        return $rules;
    }


    public function messages()
    {

        $message = [
            'code.required' => 'code不能为空',
        ];

        return $message;
    }

}