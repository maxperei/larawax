<?php

namespace App\Http\Controllers;

use App\Oauth\DiscogsOauth;
use Discogs\ClientFactory;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Session;
use OAuth\ServiceFactory;

class DiscogsController extends Controller
{
	public $serviceName = 'DiscogsOAuth';

    public function login(Session $storage, ServiceFactory $serviceFactory, Credentials $credentials)
    {
    	$serviceFactory->registerService($this->serviceName, 'App\OAuth\DiscogsOAuth');

	    /** @var $discogsOAuth DiscogsOauth */
	    $discogsOAuth = $serviceFactory->createService($this->serviceName, $credentials, $storage);

	    if (isset($_GET['oauth_token'])) {
		    $token = $storage->retrieveAccessToken($this->serviceName);

		    try {
			    $discogsOAuth->requestAccessToken(
				    $_GET['oauth_token'],
				    $_GET['oauth_verifier'],
				    $token->getRequestTokenSecret()
			    );
		    } catch (RequestException $e) {
			    return redirect('id')->with('status', 'Access token already requested!');
		    }

		    return redirect('id')->with('status', 'Successfully authenticated!');
	    }

	    $isAuthorized = true;
	    try {
		    $token = $storage->retrieveAccessToken($this->serviceName);
	    } catch (TokenNotFoundException $e) {
		    $isAuthorized = false;
	    }

	    if (!$isAuthorized) {
		    $token = $discogsOAuth->requestRequestToken();
		    $url = $discogsOAuth->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
		    return redirect($url);
	    }

	    return redirect('id')->with('status', 'Already authenticated!');
    }

    public function search(Session $storage)
    {
	    $name = (session('searchValue')) ? session('searchValue') : getenv('APP_NAME');
	    $search = request('search');
	    session()->flash('searchValue', $search);

	    $client = $this->isAuth($storage);

	    $response = $client->search([
		    'q' => $name
	    ]);

	    return view('welcome',  compact('name', 'response'));
    }

    public function identity(Session $storage)
    {
    	$client = $this->isAuth($storage);
	    try {
		    $response = $client->getOAuthIdentity();
	    } catch (\GuzzleHttp\Exception\RequestException $e) {
		    if ($e->getResponse()->getStatusCode() == '401') {
			    $storage->clearAllTokens();
			    return redirect('/discogs');
		    }
	    }
	    return view('identity', compact('response'));
    }

	public function isAuth(Session $storage)
    {
	    if ($storage->hasAccessToken('DiscogsOAuth')) {
		    $token = $storage->retrieveAccessToken('DiscogsOAuth');

		    $client = ClientFactory::factory([
			    'defaults' => [
				    'headers' => ['User-Agent' => config('services.discogs.headers.User-Agent')],
			    ]
		    ]);

		    $oauth = new Oauth1([
			    'consumer_key'    => config('services.discogs.consumer_key'),
			    'consumer_secret' => config('services.discogs.consumer_secret'),
			    'token'           => $token->getRequestToken(),
			    'token_secret'    => $token->getRequestTokenSecret()
		    ]);

		    $client->getHttpClient()->getEmitter()->attach($oauth);

		    return $client;
	    } else {
	    	return redirect('/')->with('message', 'You must authenticate to access these resources');
	    }
    }
}
