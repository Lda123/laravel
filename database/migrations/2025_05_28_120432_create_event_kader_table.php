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
        Schema::create('event_kader', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kader');
            $table->unsignedBigInteger('id_event');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('id_kader')->references('id')->on('kader')->onDelete('cascade');
            $table->foreign('id_event')->references('id')->on('list_event')->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi pendaftaran
            $table->unique(['id_kader', 'id_event']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_kader');
    }
};