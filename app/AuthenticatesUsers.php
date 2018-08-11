<?php

namespace App;


use App\OAuth\DiscogsOAuth;
use App\Repositories\UserRepository;
use Discogs\ClientFactory;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\ServiceFactory;

/**
 * Discogs API + OAuth1
 * Register and authenticate a user
 *
 * w/ session storage | user repository
 *
 * Class AuthenticatesUsers
 * @package App
 */
class AuthenticatesUsers
{
	public $serviceName = 'DiscogsOAuth';
	/**
	 * @var UserRepository
	 */
	protected $users;
	/**
	 * @var Session
	 */
	public $storage;
	/**
	 * @var $discogsOAuth DiscogsOauth
	 */
	private $discogsOAuth;
	/**
	 * @var bool
	 */
	public $hasAccessToken;
	/**
	 * @var bool
	 */
	public $hasAuthorizationState;

	/**
	 * AuthenticatesUsers constructor.
	 */
	public function __construct(UserRepository $users, Session $storage, ServiceFactory $serviceFactory, Credentials $credentials)
	{
		$this->users = $users;
		$this->storage = $storage;
		$serviceFactory->registerService($this->serviceName, 'App\OAuth\DiscogsOAuth');
		$this->discogsOAuth = $serviceFactory->createService($this->serviceName, $credentials, $storage);
		$this->hasAccessToken = $this->storage->hasAccessToken($this->serviceName);
		$this->hasAuthorizationState = $this->storage->hasAuthorizationState($this->serviceName);
	}

	/**
	 * @param $tokens array
	 */
	public function process($tokens, AuthenticatesUsersListener $listener)
	{
		if (empty($tokens)) return $this->getAuthorizationFirst();
		$authorization = $this->authorize($tokens);
		$client = $this->getDiscogsClient($authorization);
		$user = $this->signIn($client);
		return $listener->userHasLoggedIn($user);
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	private function getAuthorizationFirst()
	{
		$token = $this->discogsOAuth->requestRequestToken();
		$url = $this->discogsOAuth->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
		return redirect($url);
	}

	/**
	 * @param $tokens
	 *
	 * @return \OAuth\Common\Token\TokenInterface
	 */
	public function authorize($tokens)
	{
		if (isset($tokens['oauth_token']) && isset($tokens['oauth_verifier'])) {
			$token = $this->storage->retrieveAccessToken($this->serviceName);
			$this->discogsOAuth->requestAccessToken(
				$tokens['oauth_token'],
				$tokens['oauth_verifier'],
				$token->getRequestTokenSecret()
			);
		}

		//TODO User repository to store access token when registration
		$this->storage->storeAuthorizationState($this->serviceName, md5(rand()));
		return $this->storage->retrieveAccessToken($this->serviceName);
	}

	/**
	 * @param $authorization
	 *
	 * @return \GuzzleHttp\Command\Guzzle\GuzzleClient
	 */
	public function getDiscogsClient($authorization)
	{
		$client = ClientFactory::factory([
			'defaults' => [
				'headers' => ['User-Agent' => config('services.discogs.headers.User-Agent')],
			]
		]);

		$oauth = new Oauth1([
		  'consumer_key' => config('services.discogs.consumer_key'),
		  'consumer_secret' => config('services.discogs.consumer_secret'),
		  'token' => $authorization->getRequestToken(),
		  'token_secret' => $authorization->getRequestTokenSecret()
		]);

		$client->getHttpClient()->getEmitter()->attach($oauth);

		return $client;
	}

	/**
	 * @param $client ClientFactory
	 *
	 * @return mixed
	 */
	public function getDiscogsUser($client)
	{
		$identity = $client->getOAuthIdentity();
		return $client->getProfile([
			'username' => $identity['username']
		]);
	}

	/**
	 * @param $client
	 *
	 * @return User
	 */
	public function signIn($client)
	{
		/** @var User $user */
		$user = $this->users->findByUsernameOrCreate($this->getDiscogsUser($client));
		\Auth::login($user, true);
		return $user;
	}
}