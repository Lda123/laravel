<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventWargaTable extends Migration
{
    public function up()
    {
        Schema::create('event_warga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_event'); // Change data type to unsignedBigInteger
            $table->foreignId('id_warga')->constrained('warga')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('id_event', 'event_warga_id_event_foreign') // Add foreign key constraint
                ->references('id')->on('list_event')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_warga');
    }
}