<?php

namespace App\Rules;

use App\Models\OrganizationSettings;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ValidRecaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $settings = OrganizationSettings::current();
        $message = 'The security check could not be verified. Please complete it again and retry.';

        if (blank($settings->recaptcha_site_key) || blank($settings->recaptcha_secret_key)) {
            $fail($message);

            return;
        }

        try {
            $response = Http::asForm()->connectTimeout(3)->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $settings->recaptcha_secret_key,
                    'response' => $value,
                ]);
        } catch (ConnectionException) {
            $fail($message);

            return;
        }

        $expectedHost = preg_replace('/^www\./', '', strtolower((string) parse_url(config('app.url'), PHP_URL_HOST)));
        $verifiedHost = preg_replace('/^www\./', '', strtolower((string) $response->json('hostname')));

        if (! $response->successful() || $response->json('success') !== true || blank($expectedHost) || $verifiedHost !== $expectedHost) {
            $fail($message);
        }
    }
}
