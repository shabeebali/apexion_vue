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
Route::prefix('/admin')->group(function(){
    Route::get('/{any}',function(){
        return view('backend.app');
    })->where('any', '.*');
    Route::get('/',function(){
        return view('backend.app');
    });
});
Route::get('/test','Backend\ProductController@temp');
Route::middleware('auth')->group(function(){
    Route::get('test2',function(){
        $user=\Auth::user();
        return 'Permitted for '.$user->name;
    });
});