<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleaseLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_labels', function (Blueprint $table) {
            $table->integer('release_id')->unsigned();
            $table->string('label_name', 255)->index();
        });

        Schema::table('release_labels', function (Blueprint $table) {
        	$table->foreign('release_id')->references('id')->on('releases');
        	$table->foreign('label_name')->references('name')->on('labels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('release_labels');
    }
}
