<?php

namespace App\Mail;

use App\Models\ParticipantRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipantRegistrationUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $theme = 'ave-branca';

    /**
     * Create a new message instance.
     */
    public function __construct(public ParticipantRegistration $registration) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->registration->payment_status === 'cancelled'
                ? 'Inscrição cancelada - Corrida Ave Branca'
                : 'Atualização da inscrição - Corrida Ave Branca',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.participant-registration-updated',
            with: [
                'registration' => $this->registration,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
