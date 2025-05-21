<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingHarianTable extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_harian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warga_id');
            $table->enum('status_kesehatan', ['Sehat', 'Gejala Ringan', 'Terkena DBD']);
            $table->enum('status_lingkungan', ['Bersih', 'Kurang Bersih', 'Kotor']);
            $table->enum('kategori_masalah', ['Aman', 'Tidak Aman', 'Belum Dicek'])->default('Belum Dicek');
            $table->text('deskripsi')->nullable();
            $table->string('bukti_foto')->nullable();
            $table->date('tanggal_pantau');
            $table->unsignedBigInteger('kader_id')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();

            $table->foreign('warga_id')->references('id')->on('warga')->onDelete('cascade');
            $table->foreign('kader_id')->references('id')->on('kader')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_harian');
    }
}
