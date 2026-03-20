<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 32)->default('public');
            $table->string('path');                    // путь относительно диска, напр. media/2026/03/file.jpg
            $table->string('filename');               // оригинальное имя файла
            $table->string('mime_type', 128);
            $table->unsignedBigInteger('size')->default(0); // размер в байтах
            $table->string('alt')->nullable();        // подпись для доступности
            $table->string('title')->nullable();      // название
            $table->text('caption')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('disk');
            $table->index('mime_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
