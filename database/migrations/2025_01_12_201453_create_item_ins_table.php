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
        Schema::create('item_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itemcategory_id');
            $table->foreignId('divisi_id');
            $table->string('name');
            $table->string('price')->nullable();
            $table->string('capital_price')->nullable();
            $table->string('no_seri')->unique();
            $table->string('kode_msk')->nullable();
            $table->string('ppn')->nullable();
            $table->integer('status')->default('0');
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
        Schema::dropIfExists('item_ins');
    }
};
