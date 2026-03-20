<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->enum('type', ['category', 'tag'])->default('category')->after('slug');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
