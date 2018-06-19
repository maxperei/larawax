<?php

namespace App\Repositories;


use App\User;

class UserRepository
{
	/**
	 * @param $userData array
	 *
	 * @return $this|\Illuminate\Database\Eloquent\Model
	 */
	public function findByUsernameOrCreate($userData)
	{
		return User::firstOrCreate([
			'discogs_id' => $userData['id'],
			'username' => $userData['username'],
			'name' => $userData['name'],
			'email' => $userData['email'],
			'avatar' => $userData['avatar_url'],
			'location' => $userData['location']
		]);
	}
}