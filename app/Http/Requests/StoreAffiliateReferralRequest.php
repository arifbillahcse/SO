<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAffiliateReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            // Honeypot — must stay empty. Bots that fill every field will trip this.
            'company_website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_website.prohibited' => 'Submission rejected.',
        ];
    }
}
