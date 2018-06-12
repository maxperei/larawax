<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Oauth\DiscogsOauth;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\ServiceFactory;

class LoginWithDiscogsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	//
    }

    public function index(Session $storage, ServiceFactory $serviceFactory, Credentials $credentials)
    {
    	$serviceFactory->registerService('DiscogsOAuth', 'App\OAuth\DiscogsOAuth');

	    /** @var $discogsOAuth DiscogsOauth */
	    $discogsOAuth = $serviceFactory->createService('DiscogsOAuth', $credentials, $storage);

	    if (isset($_GET['oauth_token'])) {
		    $token = $storage->retrieveAccessToken('DiscogsOAuth');

		    $discogsOAuth->requestAccessToken(
			    $_GET['oauth_token'],
			    $_GET['oauth_verifier'],
			    $token->getRequestTokenSecret()
		    );

		    return redirect('/')->with(['oauth_token' => $_GET['oauth_token'], 'oauth_verifier' => $_GET['oauth_verifier']]);
	    }

	    $isAuthorized = true;
	    try {
		    $token = $storage->retrieveAccessToken('DiscogsOAuth');
	    } catch (\OAuth\Common\Storage\Exception\TokenNotFoundException $e) {
		    $isAuthorized = false;
	    }

	    if (!$isAuthorized) {
		    $token = $discogsOAuth->requestRequestToken();
		    $url = $discogsOAuth->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
		    return redirect($url);
	    }

	    return redirect('id');
    }
}
