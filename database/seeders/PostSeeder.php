<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Welcome to ArmTrip Blog',
                'excerpt' => 'A short welcome post for the blog section.',
                'content' => '<p>This is the first blog post. You can edit it from Admin → Blog → Posts.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'title' => 'Top 10 places to visit in Armenia',
                'excerpt' => 'A quick list of must-see places in Armenia.',
                'content' => '<p>Yerevan, Garni, Geghard, Dilijan, Sevan, Tatev…</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Draft: Food guide',
                'excerpt' => 'Work in progress.',
                'content' => '<p>Coming soon.</p>',
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($items as $data) {
            $slugBase = Str::slug($data['title']);
            $slug = $slugBase;
            $i = 2;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $slugBase.'-'.$i;
                $i++;
            }

            Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'featured_image' => null,
                    'status' => $data['status'],
                    'published_at' => $data['published_at'],
                ],
            );
        }
    }
}
