<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('intro')->nullable();
            $table->dateTime('deadline');
            $table->integer('poster_id')->unsigned()->nullable()->index();
            $table->integer('checker_id')->unsigned()->nullable()->index();
            $table->integer('status');
            $table->integer('progress')->unsigned();
            $table->timestamps();
            $table->foreign('poster_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('checker_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
