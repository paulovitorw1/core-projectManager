<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyAccount extends Mailable
{
    use Queueable, SerializesModels;

    private $otpCode;
    /**
     * Create a new message instance.
     */
    public function __construct(string $otp)
    {
        $this->otpCode = $otp;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->otpCode . ' É o seu código de acesso',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'Mail.verifyEmail',
            with: ['code' => $this->otpCode],
        );
    }
}
