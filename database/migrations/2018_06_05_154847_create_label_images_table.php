<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('label_images', function (Blueprint $table) {
		    $table->integer('label_id')->unsigned();
		    $table->integer('image_id')->unsigned();
	    });

	    Schema::table('label_images', function (Blueprint $table) {
		    $table->foreign('label_id')->references('id')->on('labels');
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
        Schema::dropIfExists('label_images');
    }
}
