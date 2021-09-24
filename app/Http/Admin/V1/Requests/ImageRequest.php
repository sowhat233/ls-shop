<?php


namespace App\Http\Admin\V1\Requests;


use App\Http\Base\BaseFormRequest;

class ImageRequest extends BaseFormRequest
{
    public function rules()
    {

        $rules = [
            'image' => ['required'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'image.required' => '图片未上传',
            'image.mimes'    => '仅支持jpg、jpeg和png格式',
        ];
    }

}