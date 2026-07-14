<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HrStaffOnboardedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
        public bool $isPasswordReset = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isPasswordReset
                ? 'Your HR portal password has been reset — Akwaaba NSS'
                : 'You have been added as HR Staff on Akwaaba NSS Portal',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.hr-staff-onboarded',
        );
    }
}
