<?php

namespace App\Mail;

use App\Models\Pembayaran;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pembayaran;
    public $owner;
    public $package;

    /**
     * Create a new message instance.
     */
    public function __construct(Pembayaran $pembayaran, $owner, $package)
    {
        $this->pembayaran = $pembayaran;
        $this->owner = $owner;
        $this->package = $package;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Approved - Subscription Activated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-approved',
            with: [
                'owner' => $this->owner,
                'pembayaran' => $this->pembayaran,
                'package' => $this->package,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
