<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories_ins', function (Blueprint $table) {
            $table->id();
            $table->integer('accessories_id');
            $table->string('price')->nullable();
            $table->string('capital_price')->nullable();
            $table->string('ppn')->nullable();
            $table->bigInteger('qty');
            $table->string('kode_msk');
            $table->date('date_in');
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
        Schema::dropIfExists('accessories_ins');
    }
};
