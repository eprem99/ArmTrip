<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'lcode' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'locale' => 'en',
                'direction' => 'ltr',
                'status' => 'active',
            ],
            [
                'lcode' => 'am',
                'name' => 'Armenian',
                'native_name' => 'Հայերեն',
                'locale' => 'am',
                'direction' => 'ltr',
                'status' => 'active',
            ],
            [
                'lcode' => 'ru',
                'name' => 'Russian',
                'native_name' => 'Русский',
                'locale' => 'ru',
                'direction' => 'ltr',
                'status' => 'active',
            ],
        ];

        foreach ($languages as $data) {
            Language::updateOrCreate(
                ['lcode' => $data['lcode']],
                $data
            );
        }
    }
}
