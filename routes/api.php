<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->group(function(){
    Route::get('/config/menu','Backend\ConfigController@menuRender');
    Route::get('/validate_token',function(){
        return 'validated';
    });
    Route::get('users','Backend\ConfigController@users');
    Route::get('/acl/roles','Backend\ConfigController@roles');
    Route::post('/acl/role/add','Backend\ConfigController@addRole');
    Route::get('/acl/role/addconfig','Backend\ConfigController@addRoleConfig');
    //Products
    Route::get('products','Backend\ProductController@index');
    Route::post('products/list_settings','Backend\ProductController@list_settings');
    Route::get('products/filterables','Backend\ProductController@get_filterables');
    Route::get('products/add','Backend\ProductController@add');
    Route::post('/products/add','Backend\ProductController@save');
    Route::get('/products/settings','Backend\ProductController@settings');
    Route::get('/products/add/form','Backend\ProductController@productsAddForm');
    // Categories
    Route::get('products/categories','Backend\TaxonomyController@index');
    Route::get('products/categories/add','Backend\TaxonomyController@add');
    Route::post('products/categories/add','Backend\TaxonomyController@save');
    Route::get('products/categories/edit/{id}','Backend\TaxonomyController@edit');
    Route::post('products/categories/edit/{id}','Backend\TaxonomyController@update');
    Route::post('products/categories/delete','Backend\TaxonomyController@delete');
    Route::get('products/categories/filterables','Backend\TaxonomyController@get_filterables');
    Route::get('products/category_type/list','Backend\TaxonomyController@type_list');
    Route::post('products/category_type/add','Backend\TaxonomyController@type_add');
    Route::get('products/category_type/edit/{id}','Backend\TaxonomyController@type_edit');
    Route::post('products/category_type/edit/{id}','Backend\TaxonomyController@type_update');
});
