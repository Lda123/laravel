<?php
// Migration file - create_saved_videos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_videos', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('kader_id');
            $table->unsignedBigInteger('video_id');
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps(); // Add created_at and updated_at
            
            // Add unique constraint to prevent duplicate saves
            $table->unique(['kader_id', 'video_id']);
            
            // Foreign key constraints
            $table->foreign('kader_id')->references('id')->on('kader')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('edukasi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_videos');
    }
};