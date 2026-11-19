<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPageSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_tag', 'hero_title', 'hero_description', 'contact_email', 'contact_phone',
        'whatsapp_number', 'whatsapp_chat_enabled', 'address', 'map_url', 'business_hours', 'form_title', 'form_description',
        'submit_label', 'success_message', 'notification_email', 'additional_notification_emails', 'next_steps', 'faqs',
    ];

    protected $casts = ['next_steps' => 'array', 'faqs' => 'array', 'whatsapp_chat_enabled' => 'boolean', 'additional_notification_emails' => 'array'];

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'hero_tag' => 'Contact Softorio',
            'whatsapp_chat_enabled' => true,
            'whatsapp_number' => '+8801779440297',
            'hero_title' => "Let's talk about your next project.",
            'hero_description' => 'Have a website to build, a cloud setup to improve or a workflow to simplify? Tell us what you are working on, where you need help and what a useful outcome would look like.',
            'form_title' => 'Tell us what you have in mind.',
            'form_description' => 'Share a little context so we can understand your requirements. If you are still exploring your options, that is a useful place to start too.',
            'submit_label' => 'Send your message',
            'success_message' => 'Thank you for getting in touch. Your message has been received and our team will review it.',
            'next_steps' => [
                ['title' => 'Share your goals', 'description' => 'Tell us about your current setup, your audience and the problem you want to solve. A website link or a short list of requirements can help.'],
                ['title' => 'Discuss the approach', 'description' => 'We review your message and clarify the scope, priorities and any technical dependencies before suggesting a direction.'],
                ['title' => 'Plan the next step', 'description' => 'Agree the work, responsibilities and practical next steps together. Your requirements guide the conversation.'],
            ],
            'faqs' => [
                ['question' => 'What should I include in my message?', 'answer' => 'Describe your project, the service you need and any useful context about your current website or systems. Include your preferred way to be contacted and any timing constraints you would like us to consider.'],
                ['question' => 'Can I get in touch before I have a complete brief?', 'answer' => 'Yes. Share the goal or problem you are exploring. We can start by discussing the information needed to define a useful scope.'],
            ],
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], static::defaults());
    }

    /** @return array{email: ?string, phone: ?string, telephone_url: ?string, social_links: array<string, string>} */
    public function publicDetails(OrganizationSettings $organization): array
    {
        $email = $this->contact_email ?: $organization->email;
        $phone = $this->contact_phone ?: $organization->telephone;
        $dial = preg_replace('/[^+0-9]/', '', (string) $phone);

        return [
            'email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null,
            'phone' => $phone,
            'telephone_url' => filled($dial) ? 'tel:'.$dial : null,
            'social_links' => $organization->publicSocialLinks(),
        ];
    }

    public function whatsappUrl(): ?string
    {
        $number = ltrim((string) $this->whatsapp_number, '+');

        return preg_match('/\A[1-9][0-9]{6,14}\z/', $number) ? 'https://wa.me/'.$number : null;
    }

    public function mapUrl(): ?string
    {
        return filter_var($this->map_url, FILTER_VALIDATE_URL) && parse_url($this->map_url, PHP_URL_SCHEME) === 'https'
            ? $this->map_url : null;
    }

    /** @return array<int, string> */
    public function notificationRecipients(): array
    {
        $addresses = [$this->notification_email, ...($this->additional_notification_emails ?? [])];
        $addresses = array_filter(array_map(fn (mixed $email): string => is_string($email) ? strtolower(trim($email)) : '', $addresses), fn (string $email): bool => (bool) filter_var($email, FILTER_VALIDATE_EMAIL));
        if ($addresses === []) {
            $addresses = preg_split('/[,;\\s]+/', (string) config('services.softorio.admin_notification_email'), flags: PREG_SPLIT_NO_EMPTY);
            $addresses = array_filter($addresses, fn (string $email): bool => (bool) filter_var($email, FILTER_VALIDATE_EMAIL));
        }

        return array_values(array_unique($addresses));
    }
}
