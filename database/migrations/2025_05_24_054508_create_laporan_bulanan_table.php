<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanBulananTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_bulanan', function (Blueprint $table) {
            $table->id(); // id utama, int(11), AUTO_INCREMENT
            $table->integer('kader_id')->index(); // int(11), dengan index
            $table->string('nama_file', 255); // varchar(255)
            $table->string('path_file', 255); // varchar(255)
            $table->timestamp('tanggal_upload')->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_bulanan');
    }
}