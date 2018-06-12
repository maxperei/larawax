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

Route::get('/', function (\OAuth\Common\Storage\Session $storage) {
	$name = (session('searchValue')) ? session('searchValue') : getenv('APP_NAME');
	$search = request('search');
	session()->flash('searchValue', $search);

	if ($storage->hasAccessToken('DiscogsOAuth') || session('oauth_token')) {
		$token = $storage->retrieveAccessToken('DiscogsOAuth');

		$client = \Discogs\ClientFactory::factory([
			'defaults' => [
				'headers' => ['User-Agent' => config('services.discogs.headers.User-Agent')],
			]
		]);

		$oauth = new GuzzleHttp\Subscriber\Oauth\Oauth1([
			'consumer_key'    => config('services.discogs.consumer_key'),
			'consumer_secret' => config('services.discogs.consumer_secret'),
			'token'           => $token->getRequestToken(),
			'token_secret'    => $token->getRequestTokenSecret()
		]);

		$client->getHttpClient()->getEmitter()->attach($oauth);

		$response = $client->search([
			'q' => $name
		]);
	}

    return view('welcome',  compact('name', 'response'));
});

Route::post('/', 'SearchController@search');

Route::get('/id', function (\OAuth\Common\Storage\Session $storage) {
	if ($storage->hasAccessToken('DiscogsOAuth') || session('oauth_token')) {
		$token = $storage->retrieveAccessToken('DiscogsOAuth');

		$client = \Discogs\ClientFactory::factory([
			'defaults' => [
				'headers' => ['User-Agent' => config('services.discogs.headers.User-Agent')],
			]
		]);

		$oauth = new GuzzleHttp\Subscriber\Oauth\Oauth1([
			'consumer_key'    => config('services.discogs.consumer_key'),
			'consumer_secret' => config('services.discogs.consumer_secret'),
			'token'           => $token->getRequestToken(),
			'token_secret'    => $token->getRequestTokenSecret()
		]);

		$client->getHttpClient()->getEmitter()->attach($oauth);

		$response = $client->getOAuthIdentity();
	}
	return view('identity', compact('response'));
});

Route::get('discogs', 'Auth\LoginWithDiscogsController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/users', function () {
	$users = DB::table('users')->get();
	return view('users', compact('users'));
});
