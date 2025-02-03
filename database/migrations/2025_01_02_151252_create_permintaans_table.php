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
        Schema::create('permintaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id_asal')->constrained('divisis')->onDelete('cascade');
            $table->foreignId('divisi_id_tujuan')->constrained('divisis')->onDelete('cascade');
            $table->string('kode')->unique();
            $table->integer('jumlah');
            $table->enum('status', ['pending', 'disetujui', 'diterima'])->default('pending');
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
        Schema::dropIfExists('permintaans');
    }
};
