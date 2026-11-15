<?php

namespace Database\Seeders;

use App\Models\OrganizationSettings;
use Illuminate\Database\Seeder;

class OrganizationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        OrganizationSettings::firstOrCreate(['id' => 1], [
            'name' => 'Softorio',
            'description' => 'Softorio drives digital transformation. We build fast, scalable, and stunning websites, cloud infrastructure, SEO, and automation.',
            'telephone' => '+880 1776060247',
            'email' => 'support@softorio.com',
        ]);
    }
}
