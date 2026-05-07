<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('guests')->default(1);
            $table->decimal('total_amount', 14, 2);
            $table->char('currency', 3)->default('USD');
            $table->string('status', 32)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['rental_id', 'status']);
            $table->index(['rental_id', 'check_in', 'check_out']);
            $table->index(['user_id', 'status']);
            $table->index('check_in');
            $table->index('check_out');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
