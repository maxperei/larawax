<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleaseImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_images', function (Blueprint $table) {
            $table->integer('release_id')->unsigned();
            $table->integer('image_id')->unsigned();
        });

        Schema::table('release_images', function (Blueprint $table) {
        	$table->foreign('release_id')->references('id')->on('releases');
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
        Schema::dropIfExists('release_images');
    }
}
