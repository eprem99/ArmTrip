<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
                $table->id();
                $table->uuid('translation_group_id');
                $table->unsignedBigInteger('content_id');
                $table->foreignId('language_id')->constrained()->restrictOnDelete();
                $table->string('type', 32);
                $table->timestamps();

                $table->unique(['content_id', 'type']);
                $table->unique(['translation_group_id', 'language_id', 'type']);
                $table->index(['type', 'language_id']);
            });
        }

        if (! Schema::hasColumn('pages', 'language_id')) {
            return;
        }

        foreach (DB::table('pages')->orderBy('id')->cursor() as $page) {
            $exists = DB::table('translations')
                ->where('content_id', $page->id)
                ->where('type', 'page')
                ->exists();
            if ($exists) {
                continue;
            }
            DB::table('translations')->insert([
                'translation_group_id' => $page->translation_group_id,
                'content_id' => $page->id,
                'language_id' => $page->language_id,
                'type' => 'page',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique(['language_id', 'slug']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['translation_group_id']);
            $table->dropColumn(['language_id', 'translation_group_id']);
        });

        $this->dedupePageSlugsForGlobalUnique();

        Schema::table('pages', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Ensure globally unique slugs before restoring pages.slug unique index.
     */
    private function dedupePageSlugsForGlobalUnique(): void
    {
        $seen = [];
        foreach (DB::table('pages')->orderBy('id')->get() as $row) {
            $slug = (string) $row->slug;
            if ($slug === '') {
                if (isset($seen[''])) {
                    $next = 'page-'.$row->id;
                    DB::table('pages')->where('id', $row->id)->update(['slug' => $next]);
                    $seen[$next] = true;
                } else {
                    $seen[''] = true;
                }

                continue;
            }
            if (isset($seen[$slug])) {
                $next = $slug.'-'.$row->id;
                DB::table('pages')->where('id', $row->id)->update(['slug' => $next]);
                $seen[$next] = true;
            } else {
                $seen[$slug] = true;
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
