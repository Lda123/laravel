<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('listpelatihan', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('nama_pelatihan'); // Nama pelatihan
            $table->date('tanggal'); // Tanggal pelatihan
            $table->string('lokasi'); // Lokasi pelatihan
            $table->string('waktu'); // Waktu pelatihan
            $table->string('biaya'); // Biaya pelatihan (dalam bentuk string untuk fleksibilitas)
            $table->timestamps(); // Created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listpelatihan');
    }
};