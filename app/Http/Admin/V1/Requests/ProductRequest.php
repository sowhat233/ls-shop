<?php


namespace App\Http\Admin\V1\Requests;


use App\Http\Base\BaseFormRequest;
use App\Enums\ProductEnums;

class ProductRequest extends BaseFormRequest
{

    private $rules = [

        'product_info'               => ['bail', 'required', 'array'],
        'product_info.category_id'   => ['bail', 'required', 'integer'],
        'product_info.name'          => ['bail', 'required', 'between:2,200'],
        'product_info.description'   => ['bail', 'required', 'between:1,255'],
        'product_info.carousels'     => ['bail', 'required', 'array', 'between:1,5'],
        'product_info.image'         => ['bail', 'required', 'max:255'],
        'product_info.sale_status'   => ['bail', 'required', 'integer'],
        'product_info.multiple_spec' => ['bail', 'required', 'integer'],
        'product_info.detail'        => ['bail', 'required'],

    ];

    //单规格验证需要验证的字段
    private $singSpecRules = [
        'product_info.stock'      => ['bail', 'required', 'integer', 'min:1'],
        'product_info.price'      => ['bail', 'required', 'numeric', 'min:0.01',],
        'product_info.cost_price' => ['bail', 'required', 'numeric', 'cost_price', 'min:0.00'],
    ];

    //多规格验证需要验证的字段
    private $multipleSpecRules = [
        'product_sku'                       => ['bail', 'required', 'array'],
        'product_sku.spec_items'            => ['bail', 'required', 'array'],
        'product_sku.sku_list'              => ['bail', 'required', 'array'],
        'product_sku.sku_list.*.attrs'      => ['bail', 'required', 'array'],
        'product_sku.sku_list.*.image'      => ['bail', 'required', 'between:1,255'],
        'product_sku.sku_list.*.stock'      => ['bail', 'required', 'integer', 'min:1'],
        'product_sku.sku_list.*.price'      => ['bail', 'required', 'numeric', 'min:0.01'],
        'product_sku.sku_list.*.cost_price' => ['bail', 'required', 'numeric', 'sku_cost_price', 'min:0.00'],
    ];


    public function rules()
    {

        if ($this->input('product_info.multiple_spec') == ProductEnums::SingleSpec) {

            //如果是单规格 添加单规格验证
            $this->rules = array_merge($this->rules, $this->singSpecRules);

        }
        else {

            //添加多规格验证规则
            $this->rules = array_merge($this->rules, $this->multipleSpecRules);

        }

        return $this->rules;

    }

    public function messages()
    {

        return [
            'product_info.required'                            => '商品信息不能为空',
            'product_info.array'                               => '商品信息的格式不正确',
            'product_info.detail.required'                     => '商品详情不能为空',
            'product_info.category_id.required'                => '请选择商品分类',
            'product_info.category_id.integer'                 => '商品分类的格式不正确',
            'product_info.name.required'                       => '商品名称不能为空',
            'product_info.name.between'                        => '商品名字的长度在 2 到 200 个字符之间',
            'product_info.description.required'                => '商品简介不能为空',
            'product_info.description.between'                 => '商品名字的长度在 1 到 255 个字符之间',
            'product_info.carousels.required'                  => '商品轮播图未上传',
            'product_info.carousels.array'                     => '商品轮播图的格式不支持',
            'product_info.carousels.between'                   => '商品轮播图数量在 1 到 5 张图片之间',
            'product_info.image.required'                      => '商品主图没上传',
            'product_info.image.max'                           => '商品主图长度不能超过255个字符',
            'product_info.sale_status.required'                => '请选择商品状态',
            'product_info.sale_status.integer'                 => '商品状态的格式不正确',
            'product_info.stock.required'                      => '商品的库存不能为空',
            'product_info.stock.integer'                       => '商品的库存必须是整数',
            'product_info.stock.min'                           => '商品的库存不能小于1',
            'product_info.price.required'                      => '商品的售价必填',
            'product_info.price.numeric'                       => '商品的售价必须是数字',
            'product_info.price.min'                           => '商品的售价不能小于0.01',
            'product_info.cost_price.required'                 => '商品的进价不能为空',
            'product_info.cost_price.numeric'                  => '商品的进价必须是数字',
            'product_info.cost_price.cost_price'               => '商品的进价不能大于等于售价',
            'product_info.cost_price.min'                      => '商品的进价不能小于0.00',
            'product_info.multiple_spec.required'              => 'sku规格类型不能为空',
            'product_info.multiple_spec.integer'               => 'sku规格类型的格式不正确',
            'product_sku.required'                             => 'sku不能为空',
            'product_sku.array'                                => 'sku的格式不正确',
            'product_sku.spec_items.required'                  => '规格列表不能为空',
            'product_sku.spec_items.array'                     => '规格列表的格式不正确',
            'product_sku.sku_list.required'                    => 'sku列表不能为空',
            'product_sku.sku_list.array'                       => 'sku的格式不正确',
            'product_sku.sku_list.*.attrs.required'            => 'sku属性值不能为空',
            'product_sku.sku_list.*.attrs.array'               => 'sku属性值的格式不正确',
            'product_sku.sku_list.*.image.required'            => 'sku图片不能为空',
            'product_sku.sku_list.*.image.between'             => 'sku图片长度不能超过255个字符',
            'product_sku.sku_list.*.stock.required'            => 'sku的库存不能为空',
            'product_sku.sku_list.*.stock.integer'             => 'sku的库存必须是整数',
            'product_sku.sku_list.*.stock.min'                 => 'sku的库存不能小于1',
            'product_sku.sku_list.*.price.required'            => 'sku的售价不能为空',
            'product_sku.sku_list.*.price.numeric'             => 'sku的售价必须是数字',
            'product_sku.sku_list.*.price.min'                 => 'sku的售价不能小于0.01',
            'product_sku.sku_list.*.cost_price.required'       => 'sku的进价不能为空',
            'product_sku.sku_list.*.cost_price.numeric'        => 'sku的进价必须是数字',
            'product_sku.sku_list.*.cost_price.sku_cost_price' => 'sku的进价不能大于等于售价',
            'product_sku.sku_list.*.cost_price.min'            => 'sku的进价不能小于0.00',

        ];

    }

}