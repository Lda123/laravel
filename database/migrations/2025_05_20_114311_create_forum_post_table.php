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
        Schema::create('forum_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null');
            $table->foreignId('kader_id')->nullable()->constrained('kader')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('forum_post')->onDelete('cascade');
            $table->string('topik', 255)->nullable();
            $table->text('pesan')->nullable();
            $table->string('gambar', 500)->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_post');
    }
};
