<?php

/**
 * 后台路由接口路由
 */

Route::group([], function () {

    //登录
    Route::post('/login', 'LoginController@store')->name('login.store');


});


Route::group(['middleware' => 'admin',], function () {

    // 上传图片
    Route::post('images', 'ImagesController@store')->name('images.store');

    Route::get('products', 'ProductsController@index')->name('products.index');
    Route::get('products/{id}', 'ProductsController@show')->where(['id' => '[0-9]+'])->name('products.show');
    Route::post('products', 'ProductsController@store')->name('products.store');
    Route::PATCH('products/{id}', 'ProductsController@update')->where(['id' => '[0-9]+'])->name('products.update');
    Route::delete('products/{id}', 'ProductsController@destroy')->where(['id' => '[0-9]+'])->name('products.destroy');
    Route::get('products/categories', 'ProductsController@categoriesList')->name('products.categoriesList');
    Route::post('products/status', 'ProductsController@changeStatus')->name('products.changeStatus');


    Route::get('categories', 'CategoriesController@index')->name('categories.index');
    Route::get('categories/{id}', 'CategoriesController@show')->where(['id' => '[0-9]+'])->name('categories.show');
    Route::post('categories', 'CategoriesController@store')->name('categories.store');
    Route::PATCH('categories/{id}', 'CategoriesController@update')->where(['id' => '[0-9]+'])->name('categories.update');
    Route::delete('categories/{id}', 'CategoriesController@destroy')->where(['id' => '[0-9]+'])->name('categories.destroy');


});

