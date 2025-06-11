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
        Schema::create('buku_panduan_kader', function (Blueprint $table) {
           $table->id(); // int(11) NOT NULL AUTO_INCREMENT
            $table->string('judul', 255);
            $table->string('penulis', 100);
            $table->string('kelas', 50)->nullable();
            $table->integer('tahun_terbit')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_pdf', 255);
            $table->string('cover_image', 255)->nullable();
            $table->integer('halaman')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_panduan_kader');
    }
};