<?php

use app\middleware\CheckToken;
use think\facade\Route;

Route::get('test/bfe', 'Index/bfe');
Route::get('test/sfc', 'Index/sfc');
Route::get('test/pp', 'Index/pp');
Route::get('test/wms', 'Index/wms');
Route::post('test/login', 'Index/loginWithToken');
Route::post('test/info', 'Index/info');
Route::post('test/logout', 'Index/logout');
// Route::get('wms/products', 'Index/products');
// Route::get('wms/suppliers', 'Index/suppliers');
// Route::get('wms/purchase', 'Index/purchase');
// Route::get('wms/orders', 'Index/orders');
// 用户
Route::group('account', function () {
    Route::post('/login', 'User/loginWithToken');
    Route::get('/info', 'User/Info');
    Route::get('/logout', 'User/logout');
});

// 产品
Route::group('wms/products', function () {
    Route::get('/', 'Product/index');
    Route::get('/:id', 'Product/read');
    Route::put('/:id', 'Product/update');
    Route::delete('/:id', 'Product/delete');
}); //->middleware([CheckToken::class]);

Route::group('wms/product', function () {
    Route::post('/', 'Product/save');
});
// 订单
Route::group('wms/orders', function () {
    Route::get('/', 'Order/index');
    Route::get('/:id', 'Order/read');
    Route::put('/:id', 'Order/update');
    Route::delete('/:id', 'Order/delete');
});

Route::group('wms/order', function () {
    Route::post('/', 'Order/save');
});

// 采购
Route::group('wms/purchase', function () {
    Route::get('/', 'Purchase/index');
    Route::get('/:id', 'Purchase/read');
    Route::post('/', 'Purchase/save');
    Route::put('/:id', 'Purchase/update');
    Route::delete('/:id', 'Purchase/delete');
});
// 供应商
Route::group('wms/suppliers', function () {
    Route::get('/', 'Supplier/index');
    Route::get('/:id', 'Supplier/read');
    Route::put('/:id', 'Supplier/update');
    Route::delete('/:id', 'Supplier/delete');
});
Route::group('wms/supplier', function () {
    Route::post('/', 'Supplier/save');
});
