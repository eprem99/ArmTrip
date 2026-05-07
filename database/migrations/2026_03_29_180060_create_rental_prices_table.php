<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('price', 12, 2);
            $table->char('currency', 3)->nullable();
            $table->timestamps();

            $table->unique(['rental_id', 'date']);
            $table->index(['rental_id', 'date']);
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_prices');
    }
};
