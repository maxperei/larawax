<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistJunctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artist_junctions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('artist_id')->unsigned();
            $table->string('artist_unique_name', 255)->nullable();
            $table->string('join_symbol', 20)->nullable();
            $table->string('role', 50)->nullable();
            $table->string('tracks', 255)->nullable();
        });

        Schema::table('artist_junctions', function (Blueprint $table) {
        	$table->foreign('artist_id')->references('id')->on('artists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artist_junctions');
    }
}
