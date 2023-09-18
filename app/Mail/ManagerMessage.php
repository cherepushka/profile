<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagerMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    private array $managerData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->managerData = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('alex.new.alex.new@gmail.com', 'Personal Cabinet')
            ->subject('Personal Cabinet | New message from client')
            ->with([
                'user_email' => $this->managerData['user_email'],
                'user_phone' => $this->managerData['user_phone'],
                'user_message' => $this->managerData['user_message'],
            ])
            ->view('/emails/manager-message');
    }
}
