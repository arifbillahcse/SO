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
        $message = $request->string('message')->toString();
        if ($request->filled('clinic_name')) {
            $message .= "\n\nClinic / lab: ".$request->string('clinic_name')->toString();
        }
        if ($request->filled('demo_plan')) {
            $message .= "\nRequested plan: ".$request->string('demo_plan')->toString();
        }

        $submission = ContactSubmission::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'service_interested' => $request->input('service_interested'),
            'message' => $message,
        ]);

        $settings = ContactPageSettings::current();

        Mail::to($settings->notificationRecipients())
            ->send(new ContactSubmissionReceived($submission));

        Mail::to($submission->email)
            ->send(new ContactSubmissionAutoReply($submission));

        if (! $request->expectsJson()) {
            return to_route('contact')->with('contact_success', $settings->success_message);
        }

        return response()->json(['message' => $settings->success_message]);
    }
}
