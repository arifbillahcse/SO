<?php

namespace Database\Seeders;

use App\Models\ClientBrand;
use Illuminate\Database\Seeder;

class ClientBrandSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Ask Media', 'Tropicana', 'Storyful', 'PepsiCo', 'NewsCorp', 'HKP Group', 'Scan Media', 'DC Innovation', 'MarketHive', 'STEM Academy'] as $index => $name) {
            ClientBrand::firstOrCreate(['name' => $name], ['sort_order' => $index * 10]);
        }
    }
}
