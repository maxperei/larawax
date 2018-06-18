<?php

namespace App;


use App\OAuth\DiscogsOAuth;
use App\Repositories\UserRepository;
use Discogs\ClientFactory;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Session;
use OAuth\ServiceFactory;

class AuthenticatesUsers
{
	public $serviceName = 'DiscogsOAuth';
	/**
	 * @var UserRepository
	 */
	private $users;
	/**
	 * @var Session
	 */
	private $storage;
	/**
	 * @var ServiceFactory
	 */
	private $serviceFactory;
	/**
	 * @var Credentials
	 */
	private $credentials;

	/**
	 * @var $discogsOAuth DiscogsOauth
	 */
	private $discogsOAuth;

	/**
	 * @var bool
	 */
	private $hasAccessToken;

	/**
	 * AuthenticatesUsers constructor.
	 */
	public function __construct(UserRepository $users, Session $storage, ServiceFactory $serviceFactory, Credentials $credentials)
	{
		$this->users = $users;
		$this->storage = $storage;
		$this->credentials = $credentials;
		$this->serviceFactory = $serviceFactory;
		$this->serviceFactory->registerService($this->serviceName, 'App\OAuth\DiscogsOAuth');
		$this->discogsOAuth = $this->serviceFactory->createService($this->serviceName, $this->credentials, $this->storage);
		$this->hasAccessToken = $this->storage->hasAccessToken($this->serviceName);
	}

	/**
	 * @param $tokens array
	 */
	public function process($tokens, AuthenticatesUsersListener $listener)
	{
		if(empty($tokens)) return $this->getAuthorizationFirst();

		$client = $this->getDiscogsClient($tokens, $listener);

		/** @var User $user */
		$user = $this->users->findByUsernameOrCreate($this->getDiscogsUser($client));

		\Auth::login($user, true);

		return $listener->userHasLoggedIn($user);
	}

	private function getAuthorizationFirst ()
	{
		$token = $this->discogsOAuth->requestRequestToken();
		$url = $this->discogsOAuth->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
		return redirect($url);
	}

	public function getDiscogsClient($tokens)
	{
		if ($this->hasAccessToken) {
			$token = $this->storage->retrieveAccessToken($this->serviceName);

			try {
				$this->discogsOAuth->requestAccessToken(
					$tokens['oauth_token'],
					$tokens['oauth_verifier'],
					$token->getRequestTokenSecret()
				);
			} catch (RequestException $e) {
				return redirect('home')->with('status', 'Access token already requested!');
			}

			$isAuthorized = true;
			try {
				$token = $this->storage->retrieveAccessToken($this->serviceName);
			} catch (TokenNotFoundException $e) {
				$isAuthorized = false;
			}

			if (!$isAuthorized) {
				$this->getAuthorizationFirst();
			}

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
		}

		return $this->getAuthorizationFirst();
	}

	public function getDiscogsUser($client)
	{
		$identity = $client->getOAuthIdentity();
		return $client->getProfile([
			'username' => $identity['username']
		]);
	}


}