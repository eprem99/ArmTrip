<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Page;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageId = Language::query()->where('lcode', 'en')->value('id')
            ?? Language::query()->orderBy('id')->value('id');

        if (! $languageId) {
            return;
        }

        $pages = [
            [
                'slug' => 'home',
                'title' => 'Home',
                'content' => '<p>Welcome to our site. This is the home page.</p>',
                'excerpt' => 'Welcome to our site.',
                'status' => 'published',
                'is_home' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'about',
                'title' => 'About',
                'content' => '<p>Learn more about us and our mission.</p>',
                'excerpt' => 'About our company.',
                'status' => 'published',
                'is_home' => false,
                'sort_order' => 2,
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact us',
                'content' => '<p>Get in touch with us. We are here to help.</p>',
                'excerpt' => 'Contact information.',
                'status' => 'published',
                'is_home' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::query()->updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            $tr = Translation::query()
                ->where('content_id', $page->id)
                ->where('type', Translation::TYPE_PAGE)
                ->first();

            if ($tr) {
                $tr->update([
                    'language_id' => $languageId,
                ]);
            } else {
                Translation::create([
                    'translation_group_id' => (string) Str::uuid(),
                    'content_id' => $page->id,
                    'language_id' => $languageId,
                    'type' => Translation::TYPE_PAGE,
                ]);
            }
        }
    }
}
