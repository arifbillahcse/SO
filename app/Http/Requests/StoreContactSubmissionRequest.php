<?php

namespace App\Http\Requests;

use App\Models\DiagnosticPageSettings;
use App\Models\OrganizationSettings;
use App\Rules\ValidRecaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'g-recaptcha-response' => OrganizationSettings::current()->recaptcha_enabled
                ? ['bail', 'required', 'string', 'max:4096', new ValidRecaptcha]
                : ['exclude'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service_interested' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'clinic_name' => ['nullable', 'required_with:demo_plan', 'string', 'max:255'],
            'demo_plan' => ['nullable', Rule::in(['Not sure yet', ...DiagnosticPageSettings::current()->planChoices()])],
            // Honeypot — must stay empty. Bots that fill every field will trip this.
            'company_website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'Please complete the security check and try again.',
            'company_website.prohibited' => 'Submission rejected.',
        ];
    }
}
