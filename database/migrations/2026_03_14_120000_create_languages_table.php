<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('lcode', 10)->unique();
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->string('locale', 20)->nullable();
            $table->string('direction', 3)->default('ltr'); // ltr, rtl
            $table->string('status', 20)->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
