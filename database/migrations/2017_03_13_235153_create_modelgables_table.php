<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelgablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modelgables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('model_id')->unsigned()->index();
            $table->integer('modelgable_id')->unsigned()->index();
            $table->string('modelgable_type')->index();
            $table->integer('required_count')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modelgables');
    }
}
