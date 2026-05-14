<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained('taxonomies')->cascadeOnDelete();
            $table->string('name');
            // Keep unique(taxonomy_id, slug) under InnoDB 767-byte index limit with utf8mb4.
            $table->string('slug', 180);
            $table->foreignId('parent_id')->nullable()->constrained('terms')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['taxonomy_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
