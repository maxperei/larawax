<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleaseVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_videos', function (Blueprint $table) {
            $table->integer('release_id')->unsigned();
            $table->integer('video_id')->unsigned();
        });

        Schema::table('release_videos', function (Blueprint $table) {
        	$table->foreign('release_id')->references('id')->on('releases');
        	$table->foreign('video_id')->references('id')->on('videos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('release_videos');
    }
}
