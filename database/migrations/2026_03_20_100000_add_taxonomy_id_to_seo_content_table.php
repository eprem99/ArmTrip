<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Superseded: taxonomy SEO uses seo_content.post_id = taxonomies.id with type=taxonomy.
 * The taxonomy_id column is removed by 2026_03_22_000000_consolidate_taxonomy_seo_post_id.php if it was added in older checkouts.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Intentionally empty — do not add taxonomy_id on new installs.
    }

    public function down(): void
    {
        //
    }
};
