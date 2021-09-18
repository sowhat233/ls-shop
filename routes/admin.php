<?php

/**
 * 后台路由接口路由
 */

Route::group([], function () {

    //登录
    Route::post('/login', 'LoginController@store')->name('login.store');
    Route::get('/test', 'TestController@test')->name('test.test');


});


Route::group(['middleware' => 'admin',], function () {

    // 上传图片
    Route::post('images', 'ImagesController@store')->name('images.store');

    Route::get('product', 'ProductController@index')->name('product.index');
    Route::get('product/{id}', 'ProductController@show')->where(['id' => '[0-9]+'])->name('product.show');
    Route::post('product', 'ProductController@store')->name('product.store');
    Route::PATCH('product/{id}', 'ProductController@update')->where(['id' => '[0-9]+'])->name('product.update');
    Route::delete('product/{id}', 'ProductController@destroy')->where(['id' => '[0-9]+'])->name('product.destroy');
    Route::get('product/category', 'ProductController@categoryList')->name('product.categoryList');
    Route::post('product/status', 'ProductController@changeStatus')->name('product.changeStatus');


    Route::get('category', 'CategoryController@index')->name('category.index');
    Route::get('category/{id}', 'CategoryController@show')->where(['id' => '[0-9]+'])->name('category.show');
    Route::post('category', 'CategoryController@store')->name('category.store');
    Route::PATCH('category/{id}', 'CategoryController@update')->where(['id' => '[0-9]+'])->name('category.update');
    Route::delete('category/{id}', 'CategoryController@destroy')->where(['id' => '[0-9]+'])->name('category.destroy');


});

