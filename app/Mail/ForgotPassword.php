<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
     /**
     * The email link instance.
     *
     * @var resetPasswordLink
     */
    public $resetPasswordLink;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($resetLink = null)
    {
        $this->resetPasswordLink = $resetLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text($this->resetPasswordLink);
    }
}
