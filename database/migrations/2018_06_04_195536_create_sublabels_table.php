<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSublabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sublabels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_name', 255)->nullable();
        });

	    Schema::table('sublabels', function (Blueprint $table) {
		    $table->foreign('unique_name')->references('unique_name')->on('labels');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sublabels');
    }
}
