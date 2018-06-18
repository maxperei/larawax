<?php
namespace App\Http\Controllers\Auth;


use App\AuthenticatesUsers;
use App\AuthenticatesUsersListener;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller implements AuthenticatesUsersListener
{
	public function login(AuthenticatesUsers $authUsers, Request $request)
	{
		$tokens = [];
		if ($request->has('oauth_token') && $request->has('oauth_verifier')) {
			$tokens = [
				'oauth_token' => $request->input('oauth_token'),
				'oauth_verifier' => $request->input('oauth_verifier')
			];
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
		return redirect('/home');
	}
}