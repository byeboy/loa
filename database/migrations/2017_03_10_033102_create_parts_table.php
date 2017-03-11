<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('intro')->nullable();
            $table->integer('material_id')->unsigned()->nullable()->index();
            $table->integer('model_id')->unsigned()->nullable()->index();
            $table->integer('cabinet_id')->unsigned()->nullable()->index();
            $table->integer('branch_id')->unsigned()->nullable()->index();
            $table->integer('count')->unsigned();
            $table->timestamps();
            $table->foreign('material_id')->references('id')->on('materials')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('model_id')->references('id')->on('models')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('cabinet_id')->references('id')->on('cabinets')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')
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
        Schema::dropIfExists('parts');
    }
}
