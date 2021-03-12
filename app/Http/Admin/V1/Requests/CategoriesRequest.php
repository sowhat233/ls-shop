<?php


namespace App\Http\Admin\V1\Requests;


use App\Http\Base\BaseFormRequest;

class CategoriesRequest extends BaseFormRequest
{


    public function rules()
    {
        $rules = [
            'name'        => 'required|between:2,20',
            'description' => 'max:200',
        ];

        return $rules;
    }


    public function messages()
    {

        $message = [
            'name.required'    => '分类名称不能为空',
            'name.between'     => '分类名称必须介于 2 - 20 个字符之间。',
            'name.description' => '分类描述不能超过 200 个字符',
        ];

        return $message;
    }

}