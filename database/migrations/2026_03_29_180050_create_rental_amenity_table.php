<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_amenity', function (Blueprint $table) {
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['rental_id', 'amenity_id']);
            $table->index('amenity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_amenity');
    }
};
