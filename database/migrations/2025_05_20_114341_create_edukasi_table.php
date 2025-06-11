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
        Schema::create('edukasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->text('isi');
            $table->enum('tipe', ['Video', 'Artikel']);
            $table->enum('kategori_pengguna', ['Warga', 'Kader']);
            $table->string('tautan', 500)->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->string('durasi', 20)->nullable();
            $table->string('kategori', 100)->nullable();
            $table->integer('views')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edukasi');
    }
};