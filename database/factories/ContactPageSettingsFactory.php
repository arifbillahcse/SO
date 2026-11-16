<?php

namespace Database\Factories;

use App\Models\ContactPageSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContactPageSettings> */
class ContactPageSettingsFactory extends Factory
{
    public function definition(): array
    {
        return ContactPageSettings::defaults();
    }
}
