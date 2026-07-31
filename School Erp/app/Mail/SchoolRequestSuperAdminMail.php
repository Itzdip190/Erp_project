<?php

namespace App\Mail;

use App\Models\SchoolRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolRequestSuperAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public SchoolRequest $schoolRequest;

    public function __construct(SchoolRequest $schoolRequest)
    {
        $this->schoolRequest = $schoolRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🏫 New School Registration Request — ' . $this->schoolRequest->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.school_request_superadmin',
        );
    }
}
