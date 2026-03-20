<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('media')->insert([
            [
                'disk' => 'uploads',
                'path' => '2026/03/sample-image-1.jpg',
                'filename' => 'sample-image-1.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 125000,
                'alt' => 'First sample image',
                'title' => 'Sample image 1',
                'caption' => null,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'disk' => 'uploads',
                'path' => '2026/03/sample-image-2.jpg',
                'filename' => 'sample-image-2.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 98000,
                'alt' => 'Second sample image',
                'title' => 'Sample image 2',
                'caption' => null,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
