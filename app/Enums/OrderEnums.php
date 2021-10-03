<?php

namespace App\Enums;

/**
 * 订单 枚举
 */
class OrderEnums
{

    //支付类型
    const WeChatPay = 1; //微信支付

    const AlipayPay = 2; //支付宝支付


    //支付类型文字数组
    const PaymentMethodName = [

        self::WeChatPay => '多规格',
        self::AlipayPay => '单规格',

    ];


    //订单状态
    const NotClosed = 1; //订单正常

    const Closed = 0; //订单关闭


    //订单状态文字数组
    const OrderStatusName = [

        self::NotClosed => '订单正常',
        self::Closed    => '订单关闭',

    ];


    //订单评价
    const NotReviewed = 1; //已评价

    const Reviewed = 0; //未评价


    //订单评价文字数组
    const ReviewStatusName = [

        self::NotReviewed => '订单未评价',
        self::Reviewed    => '订单已评价',

    ];


    //物流状态
    const NoShipped = 1; //商品 未发货

    const Delivered = 2; //商品 已发货

    const Received = 3; //商品 已收货


    //物流状态文字数组
    const DeliverStatusName = [

        self::NoShipped => '未发货',
        self::Delivered => '已发货',
        self::Received  => '已收货',
    ];

    //退款状态
    const NotRefund = 0; //未退款 下单默认状态

    const RefundApplied = 1; //申请退款

    const Refund = 2; //退款中

    const RefundSuccess = 3;  //退款成功

    const RefundFailed = 4;  //退款失败


    //退款状态文字数组
    const RefundStatusName = [

        self::NotRefund     => '未退款',
        self::RefundApplied => '申请退款',
        self::Refund        => '退款中',
        self::RefundSuccess => '退款成功',
        self::RefundFailed  => '退款失败',
    ];


}