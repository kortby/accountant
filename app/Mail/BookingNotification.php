<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingNotification extends Mailable
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
            subject: 'NEW BOOKING REQUEST: ' . $this->booking->service_type . ' from ' . $this->booking->client_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Renders resources/views/emails/booking/coach_notification.blade.php
        return new Content(
            markdown: 'emails.booking_notification',
            with: [
                'booking' => $this->booking,
            ],
        );
    }
}
