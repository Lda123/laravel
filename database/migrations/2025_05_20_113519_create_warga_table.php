<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat_lengkap');
            $table->unsignedBigInteger('rt_id'); // relasi ke tabel rt
            $table->string('telepon', 20)->nullable();
            $table->string('password');
            $table->string('foto_ktp')->nullable();
            $table->string('foto_diri_ktp')->nullable();
            $table->string('profile_pict')->nullable()->default(null); // Tambahkan ini
            $table->timestamps();

            // Foreign key ke tabel rt
            $table->foreign('rt_id')->references('id')->on('rt')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('warga');
    }
};
