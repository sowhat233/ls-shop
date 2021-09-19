<?php

namespace App\Enums;

/**
 * 商品 枚举
 */
class ProductEnums
{

    const IsMultipleSpec = 1; //多规格

    const NotMultipleSpec = 0; //单规格

    const IsLaunched = 1; //上架

    const NotLaunched = 0; //下架

    //商品状态文字数组 用于转化为文字
    const ProductStatusName = [

        self::IsLaunched  => '上架',
        self::NotLaunched => '下架',

    ];

    //商品规格文字数组 用于转化为文字
    const ProductTypeName = [

        self::IsMultipleSpec  => '多规格',
        self::NotMultipleSpec => '单规格',

    ];

}