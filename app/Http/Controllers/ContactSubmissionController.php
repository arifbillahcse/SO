<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactSubmissionRequest;
use App\Mail\ContactSubmissionAutoReply;
use App\Mail\ContactSubmissionReceived;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactSubmissionController extends Controller
{
    public function store(StoreContactSubmissionRequest $request): JsonResponse
    {
        $submission = ContactSubmission::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'service_interested' => $request->input('service_interested'),
            'message' => $request->input('message'),
        ]);

        Mail::to(config('services.softorio.admin_notification_email'))
            ->send(new ContactSubmissionReceived($submission));

        Mail::to($submission->email)
            ->send(new ContactSubmissionAutoReply($submission));

        return response()->json([
            'message' => "Thanks! We'll respond within 24 hours.",
        ]);
    }
}
