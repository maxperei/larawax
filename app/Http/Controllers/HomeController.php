<?php

namespace App\Http\Controllers;


use App\AuthenticatesUsers;
use App\User;
use OAuth\Common\Storage\Session;

class HomeController extends Controller
{
	private $authUsers;
	private $storage;

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthenticatesUsers $authUsers, Session $storage)
    {
        $this->middleware('auth');
	    $this->authUsers = $authUsers;
	    $this->storage = $storage;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$client = $this->authUsers->getDiscogsClient($this->storage->retrieveAccessToken($this->authUsers->serviceName));
    	$response = $client->getProfile(['username' => \Auth::user()->username]);
    	$user = User::find(\Auth::id());
        return view('home', compact('response', 'user'));
    }

    public function search()
    {
	    $name = session('search') ? session('search') : config('app.name');
	    $search = request('search');
	    session()->flash('search', $search);

	    $client = $this->authUsers->getDiscogsClient($this->storage->retrieveAccessToken($this->authUsers->serviceName));
	    $response = $client->search(['q' => $name]);

	    return view('search',  compact('name', 'response'));
    }

	public function find()
	{
		$search = request('search');
		return redirect(route('search'))->with('search', $search);
	}
}
