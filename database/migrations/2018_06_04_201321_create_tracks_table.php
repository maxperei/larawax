<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('position', 10)->nullable();
	        $table->string('title', 255)->nullable();
	        $table->string('duration', 10)->nullable();
	        $table->integer('release_id')->unsigned();
        });

        Schema::table('tracks', function (Blueprint $table) {
        	$table->foreign('release_id')->references('id')->on('releases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
