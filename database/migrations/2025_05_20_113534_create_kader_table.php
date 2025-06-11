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
        Schema::create('kader', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 100);
            $table->string('telepon', 15)->unique();
            $table->string('password', 255);
            $table->string('profil_pict')->nullable(); // Add this line
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kader');
    }
};