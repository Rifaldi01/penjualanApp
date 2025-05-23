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
        Schema::create('accessories_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('accessories_id');
            $table->string('qty');
            $table->string('subtotal');
            $table->date('acces_out');
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
        Schema::dropIfExists('accessories_sales');
    }
};
