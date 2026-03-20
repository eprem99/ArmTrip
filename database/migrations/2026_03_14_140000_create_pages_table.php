<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('template')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->boolean('is_home')->default(false);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('parent_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
