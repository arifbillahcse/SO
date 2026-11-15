<?php

namespace App\Mail;

use App\Models\AffiliateReferral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AffiliateReferralReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public AffiliateReferral $referral)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New affiliate program signup from '.$this->referral->referrer_name,
            replyTo: [$this->referral->referrer_email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.affiliate-referral-received',
        );
    }
}
