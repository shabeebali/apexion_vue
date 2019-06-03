<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group(function(){
    Route::prefix('/admin')->group(function(){
        Route::get('/{any}',function(){
            return view('backend.app');
        })->where('any', '.*');
    });
    Route::get('/generate_token','Auth\ApexionController@generate_token');
});
