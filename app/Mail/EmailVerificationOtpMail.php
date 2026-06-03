<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $validMinutes;
    public $userName;

    public function __construct($otp, $validMinutes, $userName = 'Pelamar')
    {
        $this->otp = $otp;
        $this->validMinutes = $validMinutes;
        $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi Email Anda (OTP)',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification_otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
