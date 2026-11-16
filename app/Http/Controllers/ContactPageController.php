<?php

namespace App\Http\Controllers;

use App\Models\ContactPageSettings;
use App\Models\OrganizationSettings;
use App\Models\Service;
use Illuminate\View\View;

class ContactPageController extends Controller
{
    public function __invoke(): View
    {
        $settings = ContactPageSettings::current();
        $organization = OrganizationSettings::current();
        $details = $settings->publicDetails($organization);

        return view('pages.contact', [
            'contactSettings' => $settings,
            'contactEmail' => $details['email'],
            'contactPhone' => $details['phone'],
            'phoneLink' => str_replace('tel:', '', $details['telephone_url'] ?? ''),
            'socialLinks' => $details['social_links'],
            'services' => Service::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get(['id', 'title']),
        ]);
    }
}
