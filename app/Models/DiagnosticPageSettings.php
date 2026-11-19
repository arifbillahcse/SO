<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticPageSettings extends Model
{
    protected $fillable = ['plans_enabled', 'plans_title', 'plans_introduction', 'pricing_faq_question', 'pricing_faq_answer', 'demo_plan_label', 'undecided_plan_label', 'plans', 'faq_title', 'faqs', 'hero_primary_text', 'hero_primary_url', 'hero_secondary_text', 'hero_secondary_url'];

    protected $casts = ['plans_enabled' => 'boolean', 'plans' => 'array', 'faqs' => 'array'];

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

        $plans = array_values(array_filter($this->plans ?? [], fn (mixed $plan): bool => is_array($plan) && ($plan['is_enabled'] ?? false) && filled($plan['name'] ?? null)));

        return array_map(function (array $plan): array {
            $url = trim((string) ($plan['button_url'] ?? ''));
            $plan['button_url'] = filter_var($url, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true) ? $url : '#demo';

            return $plan;
        }, $plans);
    }

    /** @return array<int, string> */
    public function planChoices(): array
    {
        return array_column($this->publicPlans(), 'name');
    }

    /** @return array<int, array{question: string, answer: string}> */
    public function publicFaqs(): array
    {
        if ($this->faqs !== null) {
            return $this->faqs;
        }

        return [
            ['question' => 'What is DiagnoSolution?', 'answer' => 'DiagnoSolution is software for diagnostic centers and pathology labs in Bangladesh. Reception bills the patient, the lab enters and verifies results, and the report prints on your own letterhead. The owner sees the day\'s collection and dues on a phone.'],
            ['question' => $this->pricing_faq_question, 'answer' => $this->pricing_faq_answer],
            ['question' => 'Do we need to install anything?', 'answer' => 'No. It opens in a web browser on any computer, tablet or phone. You need internet and a printer. Only analyzer connection needs a small program on one lab computer.'],
            ['question' => 'What happens if the internet goes down?', 'answer' => 'Billing and result entry need internet, so we suggest a mobile data backup at the counter. Analyzer results are kept on the lab computer and sent as soon as the connection is back, so none are lost.'],
            ['question' => 'Can it take results from our analyzers?', 'answer' => 'Yes, if the analyzer sends results by ASTM or HL7, over a serial cable or the network. Tell us your models and we will confirm before you sign up.'],
            ['question' => 'Can we move our current test list and prices?', 'answer' => 'Yes. Send us your test list, prices and referral doctors in Excel. We check the file first and import it in one go, so nothing is half loaded.'],
            ['question' => 'Will the reports look like ours?', 'answer' => 'Yes. Reports and receipts use your name, logo, letterhead margins and doctors\' signatures. We match them to the papers you use today before you go live.'],
            ['question' => 'Can someone change a result after it is verified?', 'answer' => 'Not quietly. A verified result can only be changed through a correction, which keeps the old value, the new value, the reason and who made it. Bills work the same way, with refunds and adjustments instead of edits.'],
            ['question' => 'Who owns our data?', 'answer' => 'You do. Every clinic has its own database, and you can export your data at any time.'],
            ['question' => 'How long does setup take?', 'answer' => 'Setup depends on your test catalog, report formats, data migration and analyzer requirements. We agree on the scope and schedule with you before rollout.'],
        ];
    }

    public static function isAllowedButtonUrl(string $url): bool
    {
        if (preg_match('/[\\x00-\\x20]/', $url) || str_contains($url, chr(92))) {
            return false;
        }
        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url)) {
            return true;
        }
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        return filter_var($url, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    /** @return array<int, array{text: string, url: string}> */
    public function heroButtons(?string $whatsappUrl): array
    {
        $buttons = [
            ['text' => $this->hero_primary_text ?: 'Book a demo', 'url' => $this->hero_primary_url, 'fallback' => '#demo'],
            ['text' => $this->hero_secondary_text ?: ($whatsappUrl ? 'Message us on WhatsApp' : 'Explore the software'), 'url' => $this->hero_secondary_url, 'fallback' => $whatsappUrl ?: '#views'],
        ];

        return array_map(function (array $button): array {
            $url = trim((string) $button['url']);

            return ['text' => $button['text'], 'url' => static::isAllowedButtonUrl($url) ? $url : $button['fallback']];
        }, $buttons);
    }
}
