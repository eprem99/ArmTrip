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
            $table->string('termable_type');
            $table->timestamps();

            $table->index(['termable_type', 'termable_id'], 'termables_termable_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termables');
    }
};

