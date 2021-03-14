<?php

/**
 * 微信小程序接口路由
 */

Route::group([], function () {

    //获取token
    Route::post('/token/user', 'TokenController@getToken')->name('token.getToken');

});


Route::group(['middleware' => 'wechat',], function () {


});

