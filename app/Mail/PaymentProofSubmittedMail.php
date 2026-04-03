<?php

namespace App\Mail;

use App\Models\Bank;
use App\Models\Pembayaran;
use App\Models\TipeLayanan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentProofSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pembayaran;
    public $user;
    public $package;
    public $bank;
    public $proofUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Pembayaran $pembayaran, User $user, TipeLayanan $package, Bank $bank, string $proofUrl)
    {
        $this->pembayaran = $pembayaran;
        $this->user = $user;
        $this->package = $package;
        $this->bank = $bank;
        $this->proofUrl = $proofUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Payment Proof Submitted',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-proof-submitted',
            with: [
                'pembayaran' => $this->pembayaran,
                'user' => $this->user,
                'package' => $this->package,
                'bank' => $this->bank,
                'proofUrl' => $this->proofUrl,
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
