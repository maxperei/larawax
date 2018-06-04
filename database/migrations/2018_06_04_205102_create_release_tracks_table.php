<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleaseTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_tracks', function (Blueprint $table) {
            $table->integer('release_id')->unsigned();
            $table->integer('track_id')->unsigned();
        });

	    Schema::table('release_tracks', function (Blueprint $table) {
		    $table->foreign('release_id')->references('id')->on('releases');
		    $table->foreign('track_id')->references('id')->on('tracks');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('release_tracks');
    }
}
