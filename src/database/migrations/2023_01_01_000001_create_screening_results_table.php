<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('espionage_screening_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('character_id');
            $table->unsignedBigInteger('entity_id');
            $table->enum('entity_type', ['character', 'corporation', 'alliance']);
            $table->enum('entry_type', ['wallet', 'mail', 'contract', 'contact']);
            $table->timestamp('entry_date');
            $table->text('context');
            $table->timestamps();

            $table->index('character_id');
            $table->index('entity_id');
            $table->index('entry_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('espionage_screening_results');
    }
};