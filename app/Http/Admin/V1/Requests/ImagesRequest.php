<?php


namespace App\Http\Admin\V1\Requests;


use App\Http\Base\BaseFormRequest;

class ImagesRequest extends BaseFormRequest
{
    public function rules()
    {

        $rules = [
            'images' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'images.required' => '图片未上传',
            'images.mimes'    => '仅支持jpg、jpeg和png格式',
        ];
    }

}