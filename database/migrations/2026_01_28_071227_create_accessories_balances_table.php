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
        Schema::create('accessories_balances', function (Blueprint $table) {
            $table->id();
            $table->integer('divisi_id');
            $table->integer('reject');
            $table->integer('capital_stock');
            $table->integer('retur');
            $table->integer('accessories_in');
            $table->integer('request');
            $table->integer('request_in');
            $table->integer('remainder');
            $table->integer('sale');
            $table->integer('year');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
