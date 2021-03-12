<?php


namespace App\Http\Admin\V1\Requests;

use Illuminate\Validation\Rule;
use App\Http\Base\BaseFormRequest;

class OrderRequest extends BaseFormRequest
{


    public function rules()
    {
        $rules = [

            'address_id' => [
                'required',
                // 判断用户提交的地址 ID 是否存在于数据库并且属于当前用户
                Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id),
            ],
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