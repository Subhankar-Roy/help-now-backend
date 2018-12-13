<?php

namespace App\Mail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * All these 3 variables needs to be passed to mailables
     */
    public $user; // user instance
    public $token; // token
    public $resetPasswordLink; // reset password links
    /**
     * Create a new message instance.
     * @param $user User
     * @param $token string
     * @param $resetpasslink string
     * @return void
     */
    public function __construct(User $user, $token = null, $resetpasslink = null) {
        if (func_num_args() == 3) {
            if($user != null && $token != null && $resetpasslink != null) {
                $this->user              = $user;
                $this->token             = $token;
                $this->resetPasswordLink = $resetpasslink;
            }
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $this->view('emails.forgotpassword')
            ->with([
                'user_details' => $this->user,
                'token'        => $this->token,
                'reset_link'   => env('APP_URL_FRONTEND_LOCAL').$this->resetPasswordLink
            ]);
    }
}
