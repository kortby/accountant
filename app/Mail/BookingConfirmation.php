<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Request Received: ' . $this->booking->service_type . ' (Pending Confirmation)',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Renders resources/views/emails/booking/client_confirmation.blade.php
        return new Content(
            markdown: 'emails.client_confirmation',
            with: [
                'booking' => $this->booking,
                'date' => $this->booking->booking_date->format('M jS, Y'),
            ],
        );
    }
}
