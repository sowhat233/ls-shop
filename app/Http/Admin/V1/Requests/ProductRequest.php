<?php


namespace App\Http\Admin\V1\Requests;


use App\Http\Base\BaseFormRequest;

class ProductRequest extends BaseFormRequest
{


    public function rules()
    {

        $rules = [
            'product_info'                      => ['bail', 'required'],
            'product_info.detail'               => ['bail', 'required'],
            'product_info.category_id'          => ['bail', 'required', 'integer'],
            'product_info.name'                 => ['bail', 'required', 'between:2,200'],
            'product_info.description'          => ['bail', 'required', 'between:1,255'],
            'product_info.carousels'            => ['bail', 'required', 'array', 'between:1,5'],
            'product_info.image'                => ['bail', 'required', 'max:255'],
            'product_info.sale_status'          => ['bail', 'required', 'integer'],
            'product_sku'                       => ['bail', 'required'],
            'product_sku.is_enable_spec'        => ['bail', 'required', 'integer'],
            'product_sku.spec_list'             => ['bail', 'required', 'array'],
            'product_sku.sku_list'              => ['bail', 'required', 'array'],
            'product_sku.sku_list.*.attrs'      => ['bail', 'required', 'array'],
            'product_sku.sku_list.*.image'      => ['bail', 'required', 'between:1,255'],
            'product_sku.sku_list.*.stock'      => ['bail', 'required', 'integer', 'min:1'],
            'product_sku.sku_list.*.cost_price' => ['bail', 'required', 'min:1'],
            'product_sku.sku_list.*.price'      => [
                'bail',
                'required',
                'min:1',
                function ($attribute, $price, $fail) {

                    // 获取在price对应数组里的cost_price字段 具体详见 https://learnku.com/laravel/t/48063
                    $cost_price = $this->input(str_replace('price', 'cost_price', $attribute));

                    //如果price 小于或等于 cost_price 则验证不通过
                    if ($price <= $cost_price) {

                        $fail('售价不能小于或等于成本价');
                    }

                },

            ],


        ];

        return $rules;
    }


    public function messages()
    {

        $message = [
            'product_info.required'                      => '商品信息不能为空',
            'product_info.detail.required'               => '商品详情不能为空',
            'product_info.category_id.required'          => '请选择商品分类',
            'product_info.category_id.integer'           => '商品分类的格式不正确',
            'product_info.name.required'                 => '商品名称不能为空',
            'product_info.name.between'                  => '商品名字的长度在 2 到 200 个字符之间',
            'product_info.description.required'          => '商品简介不能为空',
            'product_info.description.between'           => '商品名字的长度在 1 到 255 个字符之间',
            'product_info.carousels.required'            => '商品轮播图没上传',
            'product_info.carousels.array'               => '商品轮播图的格式不支持',
            'product_info.carousels.between'             => '商品轮播图数量在 1 到 5 张图片之间',
            'product_info.image.required'                => '商品主图没上传',
            'product_info.image.max'                     => '商品主图长度不能超过255个字符',
            'product_info.sale_status.required'          => '请选择商品状态',
            'product_info.sale_status.integer'           => '商品状态的格式不正确',
            'product_sku.required'                       => 'sku不能为空',
            'product_sku.is_enable_spec.required'        => '请选择sku规格类型',
            'product_sku.is_enable_spec.integer'         => 'sku规格类型的格式不正确',
            'product_sku.spec_list.required'             => 'spec列表不能为空',
            'product_sku.spec_list.array'                => 'spec列表的格式不正确',
            'product_sku.sku_list.required'              => 'sku列表不能为空',
            'product_sku.sku_list.array'                 => 'sku的格式不正确',
            'product_sku.sku_list.*.attrs.required'      => 'sku属性值不能为空',
            'product_sku.sku_list.*.attrs.array'         => 'sku属性值的格式不正确',
            'product_sku.sku_list.*.image.required'      => 'sku图片不能为空',
            'product_sku.sku_list.*.image.between'       => 'sku图片长度不能超过255个字符',
            'product_sku.sku_list.*.stock.required'      => 'sku的库存不能为空',
            'product_sku.sku_list.*.stock.integer'       => 'sku的库存只能为纯数字',
            'product_sku.sku_list.*.stock.min'           => 'sku的库存不能小于1',
            'product_sku.sku_list.*.price.required'      => 'sku的售价不能为空',
            'product_sku.sku_list.*.price.min'           => 'sku的售价不能小于1',
            'product_sku.sku_list.*.cost_price.required' => 'sku的进货价不能为空',
            'product_sku.sku_list.*.cost_price.min'      => 'sku的进货价不能小于1',

        ];


        return $message;

    }

}