<?php

namespace App;


interface AuthenticatesUsersListener
{
	public function userHasLoggedIn($user);
}