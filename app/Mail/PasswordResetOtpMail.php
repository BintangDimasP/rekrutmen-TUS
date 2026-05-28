<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public int $minutesValid;
    public string $userName;

    public function __construct(string $otp, int $minutesValid = 1, string $userName = '')
    {
        $this->otp          = $otp;
        $this->minutesValid = $minutesValid;
        $this->userName     = $userName;
    }

    public function build()
    {
        return $this->subject('Kode Reset Password — Rekrutmen Telkom University')
            ->view('emails.password-reset-otp');
    }
}
