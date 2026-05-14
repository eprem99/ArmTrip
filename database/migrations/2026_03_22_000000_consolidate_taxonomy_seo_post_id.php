<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Taxonomy meta title/description live in seo_content with type=taxonomy and post_id = taxonomy id.
     * If an older migration added taxonomy_id, copy values to post_id and drop taxonomy_id.
     */
    public function up(): void
    {
        if (! Schema::hasTable('seo_content')) {
            return;
        }

        // Avoid Schema::hasColumn on old MySQL/MariaDB: Laravel's column introspection
        // selects generation_expression, which does not exist on some server versions.
        if (! $this->schemaColumnExists('seo_content', 'taxonomy_id')) {
            return;
        }

        foreach (DB::table('seo_content')->where('type', 'taxonomy')->cursor() as $row) {
            if ($row->taxonomy_id !== null) {
                DB::table('seo_content')->where('id', $row->id)->update([
                    'post_id' => (int) $row->taxonomy_id,
                ]);
            }
        }

        try {
            Schema::table('seo_content', function (Blueprint $table) {
                $table->dropForeign(['taxonomy_id']);
            });
        } catch (Throwable) {
            // FK name/driver differences
        }

        try {
            Schema::table('seo_content', function (Blueprint $table) {
                $table->dropIndex(['type', 'taxonomy_id']);
            });
        } catch (Throwable) {
            try {
                Schema::table('seo_content', function (Blueprint $table) {
                    $table->dropUnique(['type', 'taxonomy_id']);
                });
            } catch (Throwable) {
                //
            }
        }

        Schema::table('seo_content', function (Blueprint $table) {
            $table->dropColumn('taxonomy_id');
        });

        $driver = Schema::getConnection()->getDriverName();
        try {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE seo_content MODIFY post_id BIGINT UNSIGNED NOT NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE seo_content ALTER COLUMN post_id SET NOT NULL');
            }
        } catch (Throwable) {
            // Column may already be NOT NULL
        }
    }

    public function down(): void
    {
        // Not reversible.
    }

    private function schemaColumnExists(string $table, string $column): bool
    {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        $database = $connection->getDatabaseName();
        $tableName = $connection->getTablePrefix().$table;

        return DB::selectOne(
            'select 1 as `exists` from information_schema.columns where table_schema = ? and table_name = ? and column_name = ? limit 1',
            [$database, $tableName, $column]
        ) !== null;
    }
};
