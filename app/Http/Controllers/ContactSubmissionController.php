<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactSubmissionRequest;
use App\Mail\ContactSubmissionAutoReply;
use App\Mail\ContactSubmissionReceived;
use App\Models\ContactPageSettings;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactSubmissionController extends Controller
{
    public function store(StoreContactSubmissionRequest $request): JsonResponse|RedirectResponse
    {
        $submission = ContactSubmission::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'service_interested' => $request->input('service_interested'),
            'message' => $request->input('message'),
        ]);

        $settings = ContactPageSettings::current();

        Mail::to($settings->notification_email ?: config('services.softorio.admin_notification_email'))
            ->send(new ContactSubmissionReceived($submission));

        Mail::to($submission->email)
            ->send(new ContactSubmissionAutoReply($submission));

        if (! $request->expectsJson()) {
            return to_route('contact')->with('contact_success', $settings->success_message);
        }

        return response()->json(['message' => $settings->success_message]);
    }
}
