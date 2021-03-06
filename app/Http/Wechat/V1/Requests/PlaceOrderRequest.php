<?php


namespace App\Http\Wechat\V1\Requests;


use App\Http\Base\BaseFormRequest;

class PlaceOrderRequest extends BaseFormRequest
{

    public function rules()
    {
        $rules = [

            'address_id'                => ['bail', 'required',],
            'product_list'              => ['bail', 'required', 'array'],
            'product_list.*.id'         => ['bail', 'required', 'integer'],
            'product_list.*.amount'     => ['bail', 'required', 'integer', 'min:1'],
            'product_list.*.type'       => ['bail', 'required', 'integer'],
            'product_list.sku'          => ['bail', 'sometimes', 'required', 'array'],
            'product_list.sku.*.id'     => ['bail', 'required', 'integer'],
            'product_list.sku.*.amount' => ['bail', 'required', 'integer', 'min:1'],

        ];

        return $rules;
    }


    public function messages()
    {

        $message = [
            'address_id.required'                => '地址未选择',
            'product_list.required'              => '请至少选择一个产品',
            'product_list.array'                 => '提交的产品格式不支持',
            'product_list.*.id.required'         => '产品id不能为空',
            'product_list.*.id.integer'          => '产品id格式不正确',
            'product_list.*.amount.required'     => '产品数量不能为空',
            'product_list.*.amount.integer'      => '产品数量格式不正确',
            'product_list.*.amount.min'          => '产品数量至少为1',
            'product_list.*.type.required'       => '产品类型不能为空',
            'product_list.*.type.integer'        => '产品类型格式不正确',
            'product_list.sku.required'          => 'sku不能为空',
            'product_list.sku.array'             => '提交的sku格式不支持',
            'product_list.sku.*.id.required'     => 'sku的id不能为空',
            'product_list.sku.*.id.integer'      => 'sku的id格式不正确',
            'product_list.sku.*.amount.required' => 'sku的数量不能为空',
            'product_list.sku.*.amount.integer'  => 'sku的数量的格式不正确',
            'product_list.sku.*.amount.min'      => 'sku数量至少为1',
        ];

        return $message;
    }

}