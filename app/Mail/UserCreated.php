<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    private array $userData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->userData = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('alex.new.alex.new@gmail.com', 'Personal Cabinet')
            ->subject('Invite to join')
            ->with([
                'email' => $this->userData['email'],
                'password' => $this->userData['password'],
            ])
            ->view('/emails/sending-messages');
    }
}
