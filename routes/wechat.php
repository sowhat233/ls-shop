<?php

/**
 * 微信小程序接口路由
 */

Route::group([], function () {

    //token登录
    Route::get('/token/user', 'TokenController@getToken')->name('token.getToken');


});


Route::group(['middleware' => 'wechat',], function () {

});

