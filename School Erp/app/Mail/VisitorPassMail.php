<?php

namespace App\Mail;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorPassMail extends Mailable
{
    use Queueable, SerializesModels;

    public Visitor $visitor;
    public ?string $schoolName;
    public ?string $schoolCode;
    public ?string $schoolLogo;
    public ?string $schoolAddress;
    public ?string $schoolPhone;
    public string $qrCodeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
        
        $school = $visitor->school ?? \App\Models\School::find($visitor->school_id);
        
        $this->schoolName    = $visitor->meta_data['school_name'] ?? $school?->name ?? 'School Campus';
        $this->schoolCode    = $visitor->meta_data['school_code'] ?? ($school?->code ? strtoupper($school->code) : 'EDUZEN');
        $this->schoolLogo    = $visitor->meta_data['school_logo'] ?? ($school?->logo_url ?? null);
        $this->schoolAddress = $visitor->meta_data['school_address'] ?? $school?->address ?? '';
        $this->schoolPhone   = $visitor->meta_data['school_phone'] ?? $school?->phone ?? '';

        // QR Code for checkout scanner / verification
        $qrData = rawurlencode($visitor->pass_number);
        $this->qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={$qrData}";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Digital Visitor Pass [{$this->visitor->pass_number}] — {$this->schoolName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.visitor_pass',
            with: [
                'visitor'       => $this->visitor,
                'schoolName'    => $this->schoolName,
                'schoolCode'    => $this->schoolCode,
                'schoolLogo'    => $this->schoolLogo,
                'schoolAddress' => $this->schoolAddress,
                'schoolPhone'   => $this->schoolPhone,
                'qrCodeUrl'     => $this->qrCodeUrl,
            ],
        );
    }
}
