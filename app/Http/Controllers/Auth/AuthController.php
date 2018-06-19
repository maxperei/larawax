<?php
namespace App\Http\Controllers\Auth;


use App\AuthenticatesUsers;
use App\AuthenticatesUsersListener;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OAuth\Common\Storage\Session;

class AuthController extends Controller implements AuthenticatesUsersListener
{
	/**
	 * Log a user
	 *
	 * @param AuthenticatesUsers $authUsers
	 * @param Request $request
	 * @param Session $storage
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function login(AuthenticatesUsers $authUsers, Request $request, Session $storage)
	{
		$tokens = [];
		if ($request->has('oauth_token') && $request->has('oauth_verifier')) {
			$tokens = [
				'oauth_token' => $request->input('oauth_token'),
				'oauth_verifier' => $request->input('oauth_verifier')
			];
		} elseif ($authUsers->hasAccessToken) {
			$tokens = (array) $storage->retrieveAccessToken($authUsers->serviceName);
			$user = $authUsers->signIn($authUsers->authorize($tokens));
			$this->userHasLoggedIn($user);
		}

		return $authUsers->process($tokens, $this);
	}

	/**
	 * When a user has successfully been logged in...
	 *
	 * @param $user
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function userHasLoggedIn($user)
	{
		return redirect()->intended('/home');
	}
}