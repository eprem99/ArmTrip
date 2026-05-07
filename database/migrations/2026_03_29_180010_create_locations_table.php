<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('kind', 32); // country, city, district
            $table->string('path', 512)->nullable()->comment('Materialized path e.g. /1/5/12/ for subtree queries');
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
            $table->index('kind');
            $table->index('path');
            $table->unique(['parent_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
