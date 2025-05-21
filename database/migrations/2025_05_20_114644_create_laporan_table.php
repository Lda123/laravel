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
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->text('alamat_detail')->nullable();
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');
            $table->foreignId('rw_id')->nullable()->constrained('rw')->onDelete('set null');
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahan')->onDelete('set null');
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->onDelete('set null');
            $table->enum('jenis_laporan', ['Jentik Nyamuk', 'Kasus DBD', 'Lingkungan Kotor']);
            $table->text('deskripsi');
            $table->enum('status', ['Pending', 'Terverifikasi', 'Selesai'])->default('Pending');
            $table->string('foto_pelaporan', 255)->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
