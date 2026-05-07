<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $defaultLangId = DB::table('languages')->orderBy('id')->value('id');
        if (! $defaultLangId) {
            return;
        }

        foreach (DB::table('taxonomies')->orderBy('id')->pluck('id') as $id) {
            if (DB::table('translations')->where('content_id', $id)->where('type', 'taxonomy')->exists()) {
                continue;
            }
            DB::table('translations')->insert([
                'translation_group_id' => (string) Str::uuid(),
                'content_id' => $id,
                'language_id' => $defaultLangId,
                'type' => 'taxonomy',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
