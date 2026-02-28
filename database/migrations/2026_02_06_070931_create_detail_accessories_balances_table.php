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
        Schema::create('detail_accessories_balances', function (Blueprint $table) {
            $table->id();
            $table->integer('accessories_id');
            $table->integer('balance_accessories_id');
            $table->integer('accessories_capital_stock');
            $table->integer('accessories_reject');
            $table->integer('accessories_retur');
            $table->integer('accessories_in');
            $table->integer('accessories_requested');
            $table->integer('accessories_requested_in');
            $table->integer('accessories_balance');
            $table->integer('accessories_sale');
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
        Schema::dropIfExists('detail_accessories_balances');
    }
};
