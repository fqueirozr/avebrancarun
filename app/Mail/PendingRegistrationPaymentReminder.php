<?php

namespace App\Mail;

use App\Models\ParticipantRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingRegistrationPaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $theme = 'ave-branca';

    /**
     * Create a new message instance.
     */
    public function __construct(
        public ParticipantRegistration $registration,
        public string $paymentUrl,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete de pagamento da inscrição - Ave Branca Run',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.pending-registration-payment-reminder',
            with: [
                'registration' => $this->registration,
                'paymentUrl' => $this->paymentUrl,
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
