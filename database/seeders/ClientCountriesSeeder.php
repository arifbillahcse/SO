<?php

namespace Database\Seeders;

use App\Models\OrganizationSettings;
use Illuminate\Database\Seeder;

class ClientCountriesSeeder extends Seeder
{
    public function run(): void
    {
        $organization = OrganizationSettings::current();

        if ($organization->client_countries === null) {
            $organization->update([
                'client_countries' => [
                    'AE', 'AM', 'AU', 'AZ', 'BD', 'BE', 'BR', 'BS', 'BW', 'CA', 'CH', 'CM', 'CN', 'CO', 'DE', 'DK', 'DO', 'EC', 'ES', 'FJ', 'FR', 'GB', 'GH', 'GR', 'GT', 'ID', 'IN', 'IT', 'JM', 'JO', 'JP', 'KE', 'KR', 'LK', 'MA', 'MX', 'MY', 'NG', 'NL', 'PH', 'PK', 'PR', 'PT', 'RO', 'SA', 'SE', 'TH', 'TR', 'TT', 'US', 'VN', 'ZA', 'ZW',
                ],
            ]);
        }
    }
}
