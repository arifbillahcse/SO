<?php

namespace App\Mail;

use App\Models\AffiliateReferral;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AffiliateReferralAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AffiliateReferral $referral)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're in — Softorio Affiliate Program",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.affiliate-referral-auto-reply',
        );
    }
}
