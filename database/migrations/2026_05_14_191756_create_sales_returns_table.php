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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id');
            $table->foreignId('user_id');

            $table->string('return_invoice');

            /*
            |--------------------------------------------------------------------------
            | partial / full
            |--------------------------------------------------------------------------
            */

            $table->enum('type', ['partial', 'full']);

            $table->bigInteger('total_return')->default(0);

            $table->text('description')->nullable();

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
        Schema::dropIfExists('sales_returns');
    }
};
