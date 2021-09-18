<?php


namespace App\Http\Admin\V1\Requests;


use App\Http\Base\BaseFormRequest;

class TestRequest extends BaseFormRequest
{


    public function rules()
    {
        $rules = [
            'name' => ['required', 'product_price'],
        ];

        return $rules;
    }


    public function messages()
    {

        $message = [
            'name.required'      => '分类名称不能为空',
            'name.product_price' => '售价有问题！',
        ];

        return $message;
    }

}