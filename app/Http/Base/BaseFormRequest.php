<?php

namespace App\Http\Base;
/**
 * 基类
 */

use Illuminate\Foundation\Http\FormRequest;
use  Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{


    public function authorize()
    {
        return true;
    }


    //重写父类方法 实现json返回
    public function failedValidation(Validator $validator)
    {
        throw (new HttpResponseException(
            responseJsonAsBadRequest($validator->errors()->first())
        ));
    }
}
