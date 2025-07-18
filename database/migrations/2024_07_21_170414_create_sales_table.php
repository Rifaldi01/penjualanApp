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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('invoice');
            $table->bigInteger('total_item');
            $table->string('total_price');
            $table->string('ppn')->nullable();
            $table->string('pph')->nullable();
            $table->string('ongkir')->default(0);
            $table->string('diskon')->default(0);
            $table->string('pay');
            $table->string('nominal_in');
            $table->string('fee');
            $table->date('deadlines')->nullable();
            $table->integer('user_id');
            $table->integer('divisi_id');
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
        Schema::dropIfExists('sales');
    }
};
