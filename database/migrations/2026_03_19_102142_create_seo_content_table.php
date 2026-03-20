<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id'); // post id, page id, or taxonomy id depending on type
            $table->string('type', 32); // post, content, category, taxonomy (taxonomy uses post_id as taxonomy PK)
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['type', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_content');
    }
};
