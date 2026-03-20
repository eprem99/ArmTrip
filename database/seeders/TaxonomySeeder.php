<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $taxonomies = [
            [
                'name' => 'Category',
                'slug' => 'category',
                'type' => Taxonomy::TYPE_CATEGORY,
                'description' => null,
                'icon' => 'folder',
            ],
            [
                'name' => 'Location',
                'slug' => 'location',
                'type' => Taxonomy::TYPE_CATEGORY,
                'description' => null,
                'icon' => 'map-pin',
            ],
            [
                'name' => 'Content Type',
                'slug' => 'content-type',
                'type' => Taxonomy::TYPE_CATEGORY,
                'description' => null,
                'icon' => 'document-text',
            ],
            [
                'name' => 'Duration',
                'slug' => 'duration',
                'type' => Taxonomy::TYPE_CATEGORY,
                'description' => null,
                'icon' => 'clock',
            ],
            [
                'name' => 'Activity',
                'slug' => 'activity',
                'type' => Taxonomy::TYPE_CATEGORY,
                'description' => null,
                'icon' => 'bolt',
            ],
            [
                'name' => 'Tags',
                'slug' => 'tags',
                'type' => Taxonomy::TYPE_TAG,
                'description' => null,
                'icon' => 'tag',
            ],
        ];

        foreach ($taxonomies as $row) {
            Taxonomy::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'description' => $row['description'],
                    'icon' => $row['icon'],
                ],
            );
        }
    }
}
