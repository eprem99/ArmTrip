<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->unsignedBigInteger('termable_id');
            // Morph class names fit here; shorter length keeps index(termable_type, termable_id) under 767 bytes (utf8mb4).
            $table->string('termable_type', 160);
            $table->timestamps();

            $table->index(['termable_type', 'termable_id'], 'termables_termable_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termables');
    }
};
