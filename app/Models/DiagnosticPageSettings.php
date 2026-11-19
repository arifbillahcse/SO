<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticPageSettings extends Model
{
    protected $fillable = ['plans_enabled', 'plans_title', 'plans_introduction', 'pricing_faq_question', 'pricing_faq_answer', 'demo_plan_label', 'undecided_plan_label', 'plans'];

    protected $casts = ['plans_enabled' => 'boolean', 'plans' => 'array'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], static::defaults());
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'plans_enabled' => true,
            'plans_title' => 'Plans',
            'plans_introduction' => 'Discuss monthly or yearly subscription options with our team. Pay by bKash, Nagad or bank transfer, and you get an invoice for each payment.',
            'pricing_faq_question' => 'How much does it cost?',
            'pricing_faq_answer' => 'Explore the plans above for the current pricing and included features. Contact us to confirm the modules, branches, setup and support your center needs.',
            'demo_plan_label' => 'Plan',
            'undecided_plan_label' => 'Not sure yet',
            'plans' => [['name' => 'Basic', 'price' => 'Ask for price', 'audience' => 'A single lab', 'features' => ['Front desk and billing', 'Laboratory and verification', 'Lab reports on your letterhead', '1 branch'], 'badge' => '', 'button_label' => 'Ask about Basic', 'is_highlighted' => false, 'is_enabled' => true], ['name' => 'Standard', 'price' => 'Ask for price', 'audience' => 'Lab and imaging under one roof', 'features' => ['Everything in Basic', 'Imaging reports', 'Owner reports and finance', 'Patient portal', 'Inventory and SMS', '1 branch'], 'badge' => 'Lab + imaging', 'button_label' => 'Ask about Standard', 'is_highlighted' => true, 'is_enabled' => true], ['name' => 'Premium', 'price' => 'Ask for price', 'audience' => 'Busy labs and groups', 'features' => ['Everything in Standard', 'Analyzer connection', 'Branches and collection centres', 'Staff attendance and payroll', 'Up to 3 branches'], 'badge' => '', 'button_label' => 'Ask about Premium', 'is_highlighted' => false, 'is_enabled' => true]],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function publicPlans(): array
    {
        if (! $this->plans_enabled) {
            return [];
        }

        return array_values(array_filter($this->plans ?? [], fn (mixed $plan): bool => is_array($plan) && ($plan['is_enabled'] ?? false) && filled($plan['name'] ?? null)));
    }

    /** @return array<int, string> */
    public function planChoices(): array
    {
        return array_column($this->publicPlans(), 'name');
    }
}
