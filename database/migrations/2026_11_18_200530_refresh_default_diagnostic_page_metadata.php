<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceDefaults(false);
    }

    public function down(): void
    {
        $this->replaceDefaults(true);
    }

    private function replaceDefaults(bool $reverse): void
    {
        foreach ($this->replacements() as $route => $fields) {
            foreach ($fields as $field => [$previous, $current]) {
                DB::table('page_seos')->where('route_name', $route)
                    ->where($field, $reverse ? $current : $previous)
                    ->update([$field => $reverse ? $previous : $current, 'updated_at' => now()]);
            }
        }
    }

    /** @return array<string, array<string, array{string, string}>> */
    private function replacements(): array
    {
        return [
            'diagnostic-management-system' => [
                'title' => ['Diagnostic Management System & Setup | Softorio', 'DiagnoCare Diagnostic Center Software in Bangladesh | Softorio'],
                'meta_description' => ['Explore Softorio\'s diagnostic management system for patients, billing, samples and reports. Software is under development; discuss availability and setup.', 'Explore DiagnoCare by Softorio for diagnostic center billing, samples, lab reports and owner operations. Request a software demonstration for your center.'],
                'content_notes' => ['Diagnostic center software exploration and setup intent. Software is under development. Preserve that qualification, distinguish planned core features from optional add-ons, and confirm availability before agreeing scope. No diagnostic-accuracy claims.', 'DiagnoCare product exploration and demo requests. Confirm module availability, pricing, hosting and analyzer compatibility before agreeing scope. No diagnostic-accuracy claims.'],
                'og_title' => ['Diagnostic Management System & Setup | Softorio', 'DiagnoCare Diagnostic Center Software in Bangladesh | Softorio'],
                'twitter_title' => ['Diagnostic Management System & Setup | Softorio', 'DiagnoCare Diagnostic Center Software in Bangladesh | Softorio'],
                'og_description' => ['Explore Softorio\'s diagnostic management system for patients, billing, samples and reports. Software is under development; discuss availability and setup.', 'Explore DiagnoCare by Softorio for diagnostic center billing, samples, lab reports and owner operations. Request a software demonstration for your center.'],
                'twitter_description' => ['Explore Softorio\'s diagnostic management system for patients, billing, samples and reports. Software is under development; discuss availability and setup.', 'Explore DiagnoCare by Softorio for diagnostic center billing, samples, lab reports and owner operations. Request a software demonstration for your center.'],
            ],
            'solutions' => [
                'meta_description' => ['Explore Softorio solutions for eCommerce, cloud infrastructure, automation and a planned diagnostic center software rollout. Discuss your next project.', 'Explore Softorio solutions for eCommerce, cloud infrastructure, automation and DiagnoCare diagnostic center software. Discuss your next project.'],
                'og_description' => ['Explore Softorio solutions for eCommerce, cloud infrastructure, automation and a planned diagnostic center software rollout. Discuss your next project.', 'Explore Softorio solutions for eCommerce, cloud infrastructure, automation and DiagnoCare diagnostic center software. Discuss your next project.'],
                'twitter_description' => ['Explore Softorio solutions for eCommerce, cloud infrastructure, automation and a planned diagnostic center software rollout. Discuss your next project.', 'Explore Softorio solutions for eCommerce, cloud infrastructure, automation and DiagnoCare diagnostic center software. Discuss your next project.'],
            ],
            'diagnostic-centers-solution' => [
                'title' => ['Diagnostic Center Software Solution & Rollout Plan | Softorio', 'Custom Diagnostic Center Software Rollout | Softorio'],
                'og_title' => ['Diagnostic Center Software Solution & Rollout Plan | Softorio', 'Custom Diagnostic Center Software Rollout | Softorio'],
                'twitter_title' => ['Diagnostic Center Software Solution & Rollout Plan | Softorio', 'Custom Diagnostic Center Software Rollout | Softorio'],
            ],
        ];
    }
};
