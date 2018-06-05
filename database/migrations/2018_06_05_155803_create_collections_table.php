<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('collections', function (Blueprint $table) {
		    $table->integer('release_id')->unsigned();
		    $table->integer('user_id')->unsigned();
	    });

	    Schema::table('collections', function (Blueprint $table) {
		    $table->foreign('release_id')->references('id')->on('releases');
		    $table->foreign('user_id')->references('id')->on('users');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections');
    }
}
