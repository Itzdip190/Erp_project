<?php

namespace App\Mail;

use App\Models\DemoBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoBookingClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public DemoBooking $booking;

    public function __construct(DemoBooking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Booking a Demo with EducoreERP!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo_booking_client',
        );
    }
}
