<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saved_edukasi_warga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->foreignId('edukasi_id')->constrained('edukasi')->onDelete('cascade');
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['warga_id', 'edukasi_id']); // Ensure unique saves
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_edukasi_warga');
    }
};