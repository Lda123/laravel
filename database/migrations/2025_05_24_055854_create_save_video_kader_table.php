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
        Schema::create('savedvideos', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->unsignedBigInteger('kader_id');
            $table->unsignedBigInteger('video_id');
            $table->timestamp('saved_at')->useCurrent();

            // Jika ingin, bisa tambahkan foreign key:
            $table->foreign('kader_id')->references('id')->on('kader')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('edukasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savedvideos');
    }
};