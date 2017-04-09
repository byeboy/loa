<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartgablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partgables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('part_id')->unsigned()->index();
            $table->integer('partgable_id')->unsigned()->index();
            $table->string('partgable_type')->index();
            $table->integer('required_count')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('part_id')->references('id')->on('parts')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partgables');
    }
}
