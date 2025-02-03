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
        Schema::create('accessories_rejectes', function (Blueprint $table) {
            $table->id();
            $table->integer('divisi_id');
            $table->string('name');
            $table->string('price')->default(0);
            $table->string('capital_price')->default(0);
            $table->string('code_acces');
            $table->string('keterangan')->nullable();
            $table->bigInteger('stok')->default(0);
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
        Schema::dropIfExists('accessories_rejectes');
    }
};
