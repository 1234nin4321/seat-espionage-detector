<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('espionage_suspicious_entities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->enum('entity_type', ['character', 'corporation', 'alliance']);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('entity_id');
            $table->index('entity_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('espionage_suspicious_entities');
    }
};