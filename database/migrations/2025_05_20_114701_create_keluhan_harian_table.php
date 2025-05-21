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
        Schema::create('keluhan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warga')->constrained('warga')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('suhu', 10)->nullable();
            $table->string('ruam', 20)->nullable();
            $table->boolean('nyeri_otot')->nullable();
            $table->boolean('mual')->nullable();
            $table->boolean('nyeri_belakang_mata')->nullable();
            $table->boolean('pendarahan')->nullable();
            $table->text('gejala_lain')->nullable();
            $table->float('akurasi_dbd')->nullable();
            $table->text('anjuran')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluhan_harian');
    }
};
