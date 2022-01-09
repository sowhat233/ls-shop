<?php

/**
 * 微信小程序接口路由
 */

Route::group([], function () {

    //获取token
    Route::post('/token/user', 'TokenController@store')->name('token.store');

});

Route::group(['middleware' => 'wechat',], function () {

    Route::post('/order', 'OrderController@store')->name('order.store');

    Route::post('/order/pay/{id}', 'OrderController@pay')->where(['id' => '[0-9]+'])->name('order.pay');

    Route::get('address', 'AddressController@index')->name('address.index');
    Route::post('/address', 'AddressController@store')->name('address.store');
    Route::post('address/{id}', 'AddressController@update')->where(['id' => '[0-9]+'])->name('address.update');
    Route::get('address/{id}/edit', 'AddressController@edit')->where(['id' => '[0-9]+'])->name('address.edit');
    Route::get('/address/last', 'AddressController@last')->name('address.last');

});

