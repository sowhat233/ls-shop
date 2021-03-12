<?php

namespace App\Enums;

/**
 * 商品 枚举
 */
class ProductEnums
{

    const EnableSpec = 1; //启用多规格

    const NotEnableSpec = 0; //不启用多规格

    const on_sale = 1; //上架

    const not_on_sale = 0; //下架


    //商品状态文字数组 用于转化为文字
    const ProductStatusName = [

        self::on_sale     => '上架',
        self::not_on_sale => '下架',

    ];

}