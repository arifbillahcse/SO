<?php

namespace Database\Seeders;

use App\Models\OrganizationSettings;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $organization = OrganizationSettings::current();

        foreach (['tos', 'privacy'] as $page) {
            foreach (config("legal.{$page}") as $field => $default) {
                $attribute = "{$page}_{$field}";

                if ($organization->getAttribute($attribute) === null) {
                    $organization->setAttribute($attribute, $default);
                }
            }
        }

        if ($organization->isDirty()) {
            $organization->save();
        }
    }
}
