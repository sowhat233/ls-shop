<?php

/**
 * 微信小程序接口路由
 */

Route::group([], function () {

    //获取token
    Route::post('/token/user', 'TokenController@store')->name('token.store');

    Route::post('/order', 'OrderController@store')->name('order.store');

});

//Route::group(['middleware' => 'wechat',], function () {
//
//    Route::post('/order', 'OrderController@store')->name('order.store');
//
//});

