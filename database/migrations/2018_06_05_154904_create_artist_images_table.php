<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('artist_images', function (Blueprint $table) {
		    $table->integer('artist_id')->unsigned();
		    $table->integer('image_id')->unsigned();
	    });

	    Schema::table('artist_images', function (Blueprint $table) {
		    $table->foreign('artist_id')->references('id')->on('artists');
		    $table->foreign('image_id')->references('id')->on('images');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artist_images');
    }
}
