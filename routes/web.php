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
	$name = (session('searchValue')) ? session('searchValue') : getenv('APP_NAME');
	$search = request('search');
	session()->flash('searchValue', $search);

	if (isset($_GET['oauth_verifier'])) {
		$oauthObject = new \App\Services\OAuthSimple();
		$scope       = 'https://api.discogs.com';
		// If we have a oauth_verifier, fetch the cookie and amend our signature array with the request
		// token and secret.
		$signatures = ['consumer_key' => config('services.discogs.consumer_key'), 'shared_secret' => config('services.discogs.consumer_secret')];
		$signatures['oauth_secret'] = $_COOKIE['oauth_token_secret'];
		$signatures['oauth_token']  = $_GET['oauth_token'];

		//dd($_GET['oauth_token']);

		// Build the request-URL
		$result = $oauthObject->sign( [
			'path'       => 'https://api.discogs.com/oauth/access_token',
			'parameters' => [
				'oauth_verifier' => $_GET['oauth_verifier'],
				'oauth_token'    => $_GET['oauth_token']
			],
			'signatures' => $signatures
		]);

		// ... and get the web page and store it as a string again.
		$ch = curl_init();
		//Set the User-Agent Identifier
		curl_setopt( $ch, CURLOPT_USERAGENT, config( 'services.discogs.headers.User-Agent' ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt( $ch, CURLOPT_URL, $result['signed_url'] );
		$r = curl_exec($ch);

		if ($r === false) {
			throw new \Exception( curl_error( $ch ), curl_errno( $ch ) );
		}

		// parse the string to get you access token
		parse_str( $r, $returned_items );
		$access_token        = $returned_items['oauth_token'];
		$access_token_secret = $returned_items['oauth_token_secret'];

		// We can use this long-term access token to request Discogs API data,
		// for example, the identity of the authenticated user.
		// All Discogs API data requests will have to be signed just as before,
		// but we can now bypass the authorization process and use the long-term
		// access token you hopefully store somewhere permanently.
		$oauth_props = [ 'oauth_token' => $access_token, 'oauth_secret' => $access_token_secret ];

		// reset the oauth object
		$oauthObject->reset();

		// rebuild it with the URL of the resource you want to access and the token/secret
		$params['path']       = "$scope/database/search";
		$params['signatures'] = $oauth_props;

		// add optional parameters as needed
		// For example: when using the search endpoint, and/or when passing pagination options
		$params['parameters'] = 'q=nevermind&artist=nirvana&per_page=3&page=1';

		$result = $oauthObject->sign($params);

		// Now that we have our signed URL, we can make one more call to the API
		// which will grant us access to an authenticated resource
		// such as http://api.discogs.com/oauth/identity
		$url = $result['signed_url'];

		curl_setopt($ch, CURLOPT_USERAGENT, config( 'services.discogs.headers.User-Agent'));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		//Execute the curl session
		$output = curl_exec($ch);

		curl_close($ch);
	}

    return view('welcome',  compact('name', 'output'));
});

Route::post('/', 'SearchController@search');

Route::get('/auth', function () {
	$oauthObject = new \App\Services\OAuthSimple();
	$scope = 'https://api.discogs.com';

	// Initialize the output in case we get stuck in the first step.
	$output = 'Authorizing...';

	// Fill in your API key/consumer key you received when you registered your
	// application with Discogs.
	$signatures = ['consumer_key' => config('services.discogs.consumer_key'), 'shared_secret' => config('services.discogs.consumer_secret')];

	// Check if verifier exists.  If not, get a request token
	if (!isset($_GET['oauth_verifier'])) {
		// To get a Request Token, we make a request to the OAuthGetRequestToken endpoint,
		// submitting the scope of the access we need (api.discogs.com)
		// and also tell Discogs where to redirect once authorization is submitted
		$result = $oauthObject->sign([
			'path' =>'https://api.discogs.com/oauth/request_token',
			'parameters'=> [
				'scope' => $scope,
				'oauth_callback' => 'http://localhost:8000'
			],
			'signatures'=> $signatures
		]);


		// The above object generates a simple URL that includes a signature, the
		// needed parameters, and the web page that will handle our request.
		// Using the cUrl libary, we send a GET request to the signed URL
		// then add the response into a string variable ($r)
		$ch = curl_init();

		if ($ch === false) {
			throw new \Exception('failed to initialize');
		}

		curl_setopt($ch, CURLOPT_USERAGENT, config('services.discogs.headers.User-Agent'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_URL, $result['signed_url']);

		$r = curl_exec($ch);

		if ($r === false) {
			throw new Exception(curl_error($ch), curl_errno($ch));
		}

		curl_close($ch);

		// Then we parse the string for the request token and the matching token secret.
		parse_str($r, $returned_items);
		$request_token = $returned_items['oauth_token'];
		$request_token_secret = $returned_items['oauth_token_secret'];

		// We store the token and secret in a cookie for later when authorization is complete
		setcookie("oauth_token_secret", $request_token_secret, time()+3600);

		// Next we generate a URL for an authorization request, then redirect to that URL
		// so the user can authorize our request.
		// The user could deny the request, so we should add some code later to handle that situation
		$result = $oauthObject->sign([
			'path'       => 'https://www.discogs.com/oauth/authorize',
			'parameters' => [
				'oauth_token' => $request_token
			],
			'signatures' => $signatures
		]);

		// Here is where we redirect
		header("Location:$result[signed_url]");
		exit;
	}
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/users', function () {
	$users = DB::table('users')->get();
	return view('users', compact('users'));
});
