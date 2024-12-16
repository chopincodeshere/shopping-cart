<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password)
    {
        //
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Forgot Password")->view('auth.mail.forgot')->with(['password' => $this->password]);
    }
}
