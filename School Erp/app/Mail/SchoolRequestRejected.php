<?php

namespace App\Mail;

use App\Models\SchoolRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolRequestRejected extends Mailable
{
    use Queueable, SerializesModels;

    public SchoolRequest $schoolRequest;
    public ?string $rejectedReason;

    public function __construct(SchoolRequest $schoolRequest, ?string $rejectedReason = null)
    {
        $this->schoolRequest  = $schoolRequest;
        $this->rejectedReason = $rejectedReason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your School Registration Request — SchoolCloud ERP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.school_request_rejected',
        );
    }
}
