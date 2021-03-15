<?php


namespace App\Http\Admin\V1\Requests;


use App\Http\Base\BaseFormRequest;

class LoginRequest extends BaseFormRequest
{


    public function rules()
    {
        $rules = [
            'username' => ['required', 'between:2,20'],
            'password' => ['required', 'alpha_dash', 'min:6'],
        ];

        return $rules;
    }


    public function messages()
    {
        $message = [
            'username.required' => '用户名不能为空',
            'name.between'      => '用户名必须介于 2 - 20 个字符之间。',
            'password.required' => '密码不能为空',
            'password.min'      => '密码至少6位数',
        ];

        return $message;
    }

}