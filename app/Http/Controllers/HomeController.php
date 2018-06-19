<?php

namespace App\Http\Controllers;


use App\AuthenticatesUsers;
use App\User;
use OAuth\Common\Storage\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AuthenticatesUsers $authUsers, Session $storage)
    {
    	$client = $authUsers->getDiscogsClient($storage->retrieveAccessToken($authUsers->serviceName));
    	$response = $client->getProfile(['username' => \Auth::user()->username]);
    	$user = User::find(\Auth::id());
        return view('home', compact('response', 'user'));
    }
}
