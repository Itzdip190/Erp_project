<?php

namespace App\Mail;

use App\Models\SchoolRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolRequestApproved extends Mailable
{
    use Queueable, SerializesModels;

    public SchoolRequest $schoolRequest;
    public string $generatedPassword;

    public function __construct(SchoolRequest $schoolRequest, string $generatedPassword)
    {
        $this->schoolRequest    = $schoolRequest;
        $this->generatedPassword = $generatedPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your School Registration Has Been Approved — SchoolCloud ERP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.school_request_approved',
        );
    }
}
