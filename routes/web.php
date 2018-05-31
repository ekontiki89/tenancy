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
    \App\Jobs\CreateHostname::dispatch("hola","asd","qwe");
    return view('welcome');
});

Route::group(['middleware' => 'tenancy.enforce'], function () {
    Auth::routes();




});

Route::get('/home', 'HomeController@index',function ()
{
    return "hola";
    \App\Jobs\CreateHostname::dispatch("hola","asd","qwe");
})->name('home');
