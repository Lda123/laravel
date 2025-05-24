<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelatihan_kader', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->unsignedBigInteger('id_kader');
            $table->unsignedBigInteger('id_pelatihan');
            // Jika ingin menambahkan foreign key, bisa diaktifkan berikut ini:
            $table->foreign('id_kader')->references('id')->on('kader')->onDelete('cascade');
            $table->foreign('id_pelatihan')->references('id')->on('listpelatihan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihan_kader');
    }
};