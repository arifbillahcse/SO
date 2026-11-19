<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'page_seos' => ['title', 'meta_description', 'og_title', 'og_description', 'twitter_title', 'twitter_description', 'content_notes', 'focus_keyword', 'secondary_keywords'],
            'diagnostic_page_settings' => ['plans_title', 'plans_introduction', 'pricing_faq_question', 'pricing_faq_answer', 'demo_plan_label', 'undecided_plan_label', 'plans'],
        ];

        DB::transaction(function () use ($tables): void {
            foreach ($tables as $table => $fields) {
                DB::table($table)->select(['id', ...$fields])->orderBy('id')->chunkById(100, function ($records) use ($table, $fields): void {
                    foreach ($records as $record) {
                        $changes = [];
                        foreach ($fields as $field) {
                            $value = $record->{$field};
                            if (is_string($value) && stripos($value, 'DiagnoCare') !== false) {
                                $changes[$field] = str_ireplace('DiagnoCare', 'DiagnoSolution', $value);
                            }
                        }
                        if ($changes !== []) {
                            DB::table($table)->where('id', $record->id)->update([...$changes, 'updated_at' => now()]);
                        }
                    }
                });
            }
        });
    }

    /** Preserve saved content when rolling back migrations. */
    public function down(): void {}
};
