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
	if (Auth::check()) return 'Welcome back, ' . Auth::user()->username;
	return 'Hi guest. <a href="' . route('login') . '">Login with Discogs</a>';
});

Route::get('login', 'Auth\AuthController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('home', 'HomeController@index')->name('home');
Route::get('search', 'HomeController@search')->name('search');
Route::post('search', 'HomeController@find')->name('find');

Route::get('/users', function () {
	$users = DB::table('users')->get();
	return view('users', compact('users'));
});
