<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_harian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warga_id'); // foreign ke warga
            $table->string('warga_nik', 20); // dari laporan_harian
            $table->string('nama_warga', 100); // dari laporan_harian
            $table->unsignedBigInteger('kader_id')->nullable(); // foreign ke kader

            // Field dari laporan_harian
            $table->date('tanggal'); // gabungan dari 'tanggal' dan 'tanggal_pantau'
            $table->enum('kategori_masalah', ['Aman', 'Tidak Aman', 'Belum Dicek'])->default('Belum Dicek');
            $table->text('deskripsi')->nullable();
            $table->string('bukti_foto', 255)->nullable();
            $table->string('status', 50)->default('Selesai'); // dari laporan_harian
            $table->timestamp('dibuat_pada')->useCurrent(); // dari tracking_harian
            $table->timestamps(); // created_at dan updated_at

            // Relasi
            $table->foreign('warga_id')->references('id')->on('warga')->onDelete('cascade');
            $table->foreign('kader_id')->references('id')->on('kader')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_harian');
    }
};
