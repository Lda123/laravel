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
        Schema::create('list_event', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->string('waktu', 50)->nullable();
            $table->string('biaya', 50)->nullable();
            $table->enum('kategori_pengguna', ['warga', 'kader'])->default('warga');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_event');
    }
};
