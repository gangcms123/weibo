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

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');
Route::resource('users', 'UsersController');

Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');



Route::group(['prefix' => 'password'], function () {
    Route::get('email', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
    Route::post('reset/{token?}', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.reset');
    Route::get('reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.edit');
    Route::post('resets', 'Auth\ResetPasswordController@reset')->name('password.update');
});

Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);