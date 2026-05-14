<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('name');
            // Shorter than default 191 so unique(parent_id, slug) stays under InnoDB 767-byte index limit (utf8mb4).
            $table->string('slug', 180);
            $table->string('kind', 32); // country, city, district
            $table->string('path', 512)->nullable()->comment('Materialized path e.g. /1/5/12/ for subtree queries');
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
            $table->index('kind');
            // MySQL utf8mb4: full index on 512-char column exceeds 767-byte limit; SQLite has no column-prefix index syntax.
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->index([DB::raw('path(191)')], 'locations_path_index');
            } else {
                $table->index('path');
            }
            $table->unique(['parent_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
