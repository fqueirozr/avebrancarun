<?php

namespace App\Mail;

use App\Models\ShirtOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingShirtOrderPaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $theme = 'ave-branca';

    public function __construct(
        public ShirtOrder $shirtOrder,
        public string $paymentUrl,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pagamento do item avulso aguardando confirmação - Ave Branca Run',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.pending-shirt-order-payment-reminder',
            with: [
                'shirtOrder' => $this->shirtOrder,
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
