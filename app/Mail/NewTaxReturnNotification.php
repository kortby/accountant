<?php

namespace App\Mail;

use App\Models\TaxReturn;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTaxReturnNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public TaxReturn $taxReturn,
        public string $initialMessage
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Tax Return Submission - ' . $this->taxReturn->user->first_name . ' ' . $this->taxReturn->user->last_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new_tax_return_notification',
            with: [
                'taxReturn' => $this->taxReturn,
                'clientName' => $this->taxReturn->user->first_name . ' ' . $this->taxReturn->user->last_name,
                'clientEmail' => $this->taxReturn->user->email,
                'clientPhone' => $this->taxReturn->user->phone,
                'taxYear' => $this->taxReturn->tax_year,
                'submittedAt' => $this->taxReturn->submitted_at,
                'initialMessage' => $this->initialMessage,
                'returnId' => $this->taxReturn->id,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
