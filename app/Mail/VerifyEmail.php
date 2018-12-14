<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $first_name; // first name of user
    public $verification_link; // verification link
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($first_name, $verification_link)
    {
        $this->first_name        = $first_name;
        $this->verification_link = $verification_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->view('emails.signupverification')
            ->with([
                'first_name' => $this->first_name ,
                'reset_link'   => env('APP_URL_FRONTEND_LOCAL').$this->verification_link
            ]);
    }
}
