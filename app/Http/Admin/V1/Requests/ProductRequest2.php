<?php


namespace App\Http\Admin\V1\Requests2;


use App\Http\Base\BaseFormRequest2;
use App\Enums\ProductEnums2;

class ProductRequest extends BaseFormRequest
{


    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);


        //如果是单规格 那就验证product_info下的stock、cost_price、price
        if ($this->input('is_multiple_spec') === ProductEnums::IsMultipleSpec) {

        }
        else {

        }
    }

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
            'product_info.is_launched'          => ['bail', 'required', 'integer'],
            'product_info.is_multiple_spec'        => ['bail', 'required', 'integer'],
            'product_info.stock'                => ['sometimes', 'bail', 'required', 'integer', 'min:1'],
            'product_info.cost_price'           => ['sometimes', 'bail', 'required', 'numeric', 'min:0.01'],
            'product_info.price'                => ['sometimes', 'bail', 'required', 'numeric', 'min:0.00',],
            'product_sku'                       => ['sometimes', 'bail', 'required'],
            'product_sku.spec_items'            => ['sometimes', 'bail', 'required', 'array'],
            'product_sku.sku_list'              => ['sometimes', 'bail', 'required', 'array'],
            'product_sku.sku_list.*.attrs'      => ['sometimes', 'bail', 'required', 'array'],
            'product_sku.sku_list.*.image'      => ['sometimes', 'bail', 'required', 'between:1,255'],
            'product_sku.sku_list.*.stock'      => ['sometimes', 'bail', 'required', 'integer', 'min:1'],
            'product_sku.sku_list.*.cost_price' => ['sometimes', 'bail', 'required', 'min:0.01'],
            'product_sku.sku_list.*.price'      => [
                'sometimes',
                'bail',
                'required',
                'numeric',
                'min:0.01',
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
            'product_info.carousels.required'            => '商品轮播图未上传',
            'product_info.carousels.array'               => '商品轮播图的格式不支持',
            'product_info.carousels.between'             => '商品轮播图数量在 1 到 5 张图片之间',
            'product_info.image.required'                => '商品主图没上传',
            'product_info.image.max'                     => '商品主图长度不能超过255个字符',
            'product_info.is_launched.required'          => '请选择商品状态',
            'product_info.is_launched.integer'           => '商品状态的格式不正确',
            'product_info.stock.required'                => '商品的库存不能为空',
            'product_info.stock.integer'                 => '商品的库存必须是整数',
            'product_info.stock.min'                     => '商品的库存不能小于1',
            'product_info.price.required'                => '商品的售价必填',
            'product_info.price.numeric'                 => '商品的售价必须是数字',
            'product_info.price.min'                     => '商品的售价不能小于0',
            'product_info.cost_price.required'           => '商品的进价不能为空',
            'product_info.cost_price.numeric'            => '商品的进价必须是数字',
            'product_info.is_multiple_spec.required'        => 'sku规格类型不能为空',
            'product_info.is_multiple_spec.integer'         => 'sku规格类型的格式不正确',
            'product_sku.required'                       => 'sku不能为空',
            'product_sku.spec_items.required'            => '规格列表不能为空',
            'product_sku.spec_items.array'               => '规格列表的格式不正确',
            'product_sku.sku_list.required'              => 'sku列表不能为空',
            'product_sku.sku_list.array'                 => 'sku的格式不正确',
            'product_sku.sku_list.*.attrs.required'      => 'sku属性值不能为空',
            'product_sku.sku_list.*.attrs.array'         => 'sku属性值的格式不正确',
            'product_sku.sku_list.*.image.required'      => 'sku图片不能为空',
            'product_sku.sku_list.*.image.between'       => 'sku图片长度不能超过255个字符',
            'product_sku.sku_list.*.stock.required'      => 'sku的库存不能为空',
            'product_sku.sku_list.*.stock.integer'       => 'sku的库存必须是整数',
            'product_sku.sku_list.*.stock.min'           => 'sku的库存不能小于1',
            'product_sku.sku_list.*.price.required'      => 'sku的售价不能为空',
            'product_sku.sku_list.*.price.numeric'       => 'sku的售价必须是数字',
            'product_sku.sku_list.*.price.min'           => 'sku的售价不能小于0.01',
            'product_sku.sku_list.*.cost_price.required' => 'sku的进价不能为空',
            'product_sku.sku_list.*.cost_price.numeric'  => 'sku的进价必须是数字',
            'product_sku.sku_list.*.cost_price.min'      => 'sku的进价不能小于0.01',

        ];


        return $message;

    }

}