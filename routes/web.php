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

Route::get('/', 'DiscogsController@search');

Route::get('/discogs', 'DiscogsController@login');

Route::post('/', 'SearchController@search');

Route::get('/id', 'DiscogsController@identity');

Route::get('/col', 'DiscogsController@collection')->name('collection');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/users', function () {
	$users = DB::table('users')->get();
	return view('users', compact('users'));
});
