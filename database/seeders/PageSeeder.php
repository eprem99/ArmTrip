<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
