<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAffiliateReferralRequest;
use App\Mail\AffiliateReferralAutoReply;
use App\Mail\AffiliateReferralReceived;
use App\Models\AffiliateReferral;
use App\Models\ContactPageSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class AffiliateReferralController extends Controller
{
    public function store(StoreAffiliateReferralRequest $request): JsonResponse
    {
        $referral = AffiliateReferral::create([
            'referrer_name' => $request->input('name'),
            'referrer_email' => $request->input('email'),
            'referrer_phone' => $request->input('phone'),
            'message' => $request->input('message'),
        ]);

        Mail::to(ContactPageSettings::current()->notificationRecipients())
            ->send(new AffiliateReferralReceived($referral));

        Mail::to($referral->referrer_email)
            ->send(new AffiliateReferralAutoReply($referral));

        return response()->json([
            'message' => 'Thanks for your referral. Our team will review it and follow up.',
        ]);
    }
}
