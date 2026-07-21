<?php

namespace App\Mail;

use App\Models\ShirtOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShirtOrderUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $theme = 'ave-branca';

    /**
     * Create a new message instance.
     */
    public function __construct(public ShirtOrder $shirtOrder)
    {
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->shirtOrder->payment_status === 'cancelled'
                ? 'Pedido de camiseta cancelado - Ave Branca Run'
                : 'Atualização do pagamento da camiseta - Ave Branca Run',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.shirt-order-updated',
            with: ['shirtOrder' => $this->shirtOrder],
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
