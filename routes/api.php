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
    return response()->json(['username'=>$request->user()->name]);
});
Route::middleware(['auth:api'])->group(function(){
    Route::get('/config/menu','Backend\ConfigController@menuRender');
    Route::get('/validate_token',function(){
        return 'validated';
    });
    //Dashboard
    Route::get('dashboard','Backend\DashboardController@dashboard');
    Route::get('dashboard/quote','Backend\DashboardController@quote');
    //Settings
    Route::get('settings/get_menu','Backend\ConfigController@getMenu');
    Route::get('settings/permissions','Backend\ConfigController@permissions');
    Route::get('settings/users','Backend\ConfigController@users');
    Route::post('settings/users/delete','Backend\ConfigController@deleteUser');
    Route::get('settings/users/add','Backend\ConfigController@addUser');
    Route::post('settings/users/add','Backend\ConfigController@saveUser');
    Route::get('settings/users/edit/{id}','Backend\ConfigController@editUser');
    Route::post('settings/users/edit/change_password/{id}','Backend\ConfigController@changePasswordUser');
    Route::post('settings/users/edit/{id}','Backend\ConfigController@updateUser');
    Route::get('settings/users/filterables','Backend\ConfigController@get_users_filterables');
    Route::get('settings/users/roles','Backend\ConfigController@user_roles');
    Route::post('settings/users/roles/add','Backend\ConfigController@addRole');
    Route::get('settings/users/roles/edit/{id}','Backend\ConfigController@editRole');
    Route::post('settings/users/roles/edit/{id}','Backend\ConfigController@updateRole');
    Route::get('/acl/role/addconfig','Backend\ConfigController@addRoleConfig');
    Route::post('settings/users/roles/delete','Backend\ConfigController@delete');
    Route::get('settings/zones','Backend\ConfigController@zones');
    Route::post('settings/save','Backend\ConfigController@settings_save');
    //Products
    Route::get('products','Backend\ProductController@index');
    Route::get('products/view/{id}','Backend\ProductController@view');
    Route::get('products/get_menu','Backend\ProductController@getMenu');
    Route::post('products/list_settings','Backend\ProductController@list_settings');
    Route::get('products/filterables','Backend\ProductController@get_filterables');
    Route::get('products/add','Backend\ProductController@add');
    Route::get('products/edit/{id}','Backend\ProductController@edit');
    Route::post('products/edit/{id}','Backend\ProductController@update');
    Route::post('products/delete','Backend\ProductController@delete');
    Route::post('products/tally_sync','Backend\ProductController@tally_sync');
    Route::get('products/export','Backend\ProductController@export');
    Route::post('products/import','Backend\ProductController@import');
    Route::post('/products/add','Backend\ProductController@save');
    Route::post('/products/sample_event','Backend\ProductController@sample_event');
    Route::get('/products/settings','Backend\ProductController@settings');
    Route::get('/products/add/form','Backend\ProductController@productsAddForm');
    Route::get('/products/pending_count','Backend\ProductController@pending_count');
    Route::get('/products/tally_count','Backend\ProductController@tally_count');
    Route::get('products/pricelist','Backend\ProductController@pricelist');
    Route::post('products/pricelist/add','Backend\ProductController@save_pricelist');
    Route::get('products/pricelist/edit/{id}','Backend\ProductController@edit_pricelist');
    Route::post('products/pricelist/edit/{id}','Backend\ProductController@update_pricelist');
    Route::post('products/pricelist/delete','Backend\ProductController@delete_pricelist');
    Route::get('products/search','Backend\ProductController@search');
    // Categories
    Route::get('products/categories','Backend\TaxonomyController@index');
    Route::get('products/categories/export','Backend\TaxonomyController@export');
    Route::post('products/categories/import','Backend\TaxonomyController@import');
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
    Route::post('products/category_type/delete','Backend\TaxonomyController@type_delete');
    Route::get('products/code_order','Backend\TaxonomyController@code_order');
    Route::post('products/code_order','Backend\TaxonomyController@save_code_order');
    Route::get('products/categories/types','Backend\TaxonomyController@types');


    //Customer

    Route::get('customers','Backend\CustomerController@index');
    Route::get('customers/get_menu','Backend\CustomerController@getMenu');
    Route::post('customers/add','Backend\CustomerController@save');
    Route::post('customers/delete','Backend\CustomerController@delete');
    Route::get('customers/edit/{id}','Backend\CustomerController@edit');
    Route::get('customers/search','Backend\CustomerController@search');
    Route::post('customers/import','Backend\CustomerController@import');
    Route::post('customers/edit/{id}','Backend\CustomerController@update');
    //Warehouse

    Route::get('products/warehouses','Backend\WarehouseController@index');
    Route::post('products/warehouse/add','Backend\WarehouseController@save');
    Route::post('logout','Auth\LoginController@logout');

    //Sale
    Route::get('sale/get_menu','Backend\SaleController@getMenu');
    Route::get('sale/orders','Backend\SaleController@index');
    Route::post('sale/orders/add','Backend\SaleController@save');
    Route::get('sale/orders/view/{id}','Backend\SaleController@view');
    Route::get('sale/orders/getpl','Backend\SaleController@getPl');
    Route::get('sale/orders/users','Backend\SaleController@users');
    Route::get('sale/orders/getrate','Backend\SaleController@getRate');
    Route::post('sale/orders/complete','Backend\SaleController@complete');
});
Route::post('login','Auth\LoginController@login');
