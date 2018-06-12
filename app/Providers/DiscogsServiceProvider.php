<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\ServiceFactory;

class DiscogsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->bind('OAuth\Common\Storage\Session', function ($app) {
    		return new Session();
	    });

    	$this->app->bind('\OAuth\ServiceFactory', function ($app) {
    		return new ServiceFactory();
	    });

    	$this->app->bind('OAuth\Common\Consumer\Credentials', function ($app) {
		    return new Credentials(
			    config('services.discogs.consumer_key'),
			    config('services.discogs.consumer_secret'),
			    config('services.discogs.redirect')
		    );
	    });
    }
}
