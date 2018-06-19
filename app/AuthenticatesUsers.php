<?php

namespace App;


use App\OAuth\DiscogsOAuth;
use App\Repositories\UserRepository;
use Discogs\ClientFactory;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
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
	 * AuthenticatesUsers constructor.
	 */
	public function __construct(UserRepository $users, Session $storage, ServiceFactory $serviceFactory, Credentials $credentials)
	{
		$this->users = $users;
		$this->storage = $storage;
		$serviceFactory->registerService($this->serviceName, 'App\OAuth\DiscogsOAuth');
		$this->discogsOAuth = $serviceFactory->createService($this->serviceName, $credentials, $this->storage);
		$this->hasAccessToken = $this->storage->hasAccessToken($this->serviceName);
	}

	/**
	 * @param $tokens array
	 */
	public function process($tokens, AuthenticatesUsersListener $listener)
	{
		if (empty($tokens)) return $this->getAuthorizationFirst();
		$client = $this->authorize($tokens);
		$user = $this->signIn($client);
		return $listener->userHasLoggedIn($user);
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	private function getAuthorizationFirst ()
	{
		$token = $this->discogsOAuth->requestRequestToken();
		$url = $this->discogsOAuth->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
		return redirect($url);
	}

	/**
	 * @param $tokens
	 *
	 * @return \GuzzleHttp\Command\Guzzle\GuzzleClient|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function authorize($tokens)
	{
		if ($this->hasAccessToken) {
			if (!empty($tokens) && isset($tokens['oauth_token']) && isset($tokens['oauth_token'])) {
				$token = $this->storage->retrieveAccessToken($this->serviceName);
				$accessToken = $this->discogsOAuth->requestAccessToken(
					$tokens['oauth_token'],
					$tokens['oauth_verifier'],
					$token->getRequestTokenSecret()
				);
			}

			try {
				$tokens = $this->storage->retrieveAccessToken($this->serviceName);
			} catch (TokenNotFoundException $e) {
				$this->getAuthorizationFirst();
			}

			return $this->getDiscogsClient($tokens);
		}

		return $this->getAuthorizationFirst();
	}

	/**
	 * TODO: Dependency injection via service
	 *
	 * @param $isAuthorized
	 *
	 * @return \GuzzleHttp\Command\Guzzle\GuzzleClient
	 */
	public function getDiscogsClient($isAuthorized)
	{
		if ($isAuthorized) {
			$client = ClientFactory::factory([
				'defaults' => [
					'headers' => ['User-Agent' => config('services.discogs.headers.User-Agent')],
				]
			]);

			$oauth = new Oauth1([
			  'consumer_key' => config('services.discogs.consumer_key'),
			  'consumer_secret' => config('services.discogs.consumer_secret'),
			  'token' => $isAuthorized->getRequestToken(),
			  'token_secret' => $isAuthorized->getRequestTokenSecret()
			]);

			$client->getHttpClient()->getEmitter()->attach($oauth);

			return $client;
		}
	}

	/**
	 * @param $client
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