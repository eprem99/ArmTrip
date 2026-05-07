<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rental_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->unsignedSmallInteger('max_guests')->default(1);
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('beds')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 512)->nullable();
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('ratings_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_featured']);
            $table->index('location_id');
            $table->index('rental_type_id');
            $table->index('base_price');
            $table->index('max_guests');
            $table->index('rating_average');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
