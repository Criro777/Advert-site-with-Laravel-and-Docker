<?php

namespace App\Mail\Auth;

use App\Entity\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;


    /**
     * Create a new message instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): VerifyMail
    {
        return $this
            ->subject('Signup confirmation')
            ->markdown('emails.auth.register.verify');
    }
}
