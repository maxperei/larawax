<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('artist_releases', function (Blueprint $table) {
		    $table->integer('release_id')->unsigned();
		    $table->integer('artist_junction_id')->unsigned();
	    });

	    Schema::table('artist_releases', function (Blueprint $table) {
		    $table->foreign('release_id')->references('id')->on('tracks');
		    $table->foreign('artist_junction_id')->references('id')->on('artist_junctions');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artist_releases');
    }
}
