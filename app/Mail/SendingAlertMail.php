<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Class SendingAlertMail
 * @package App\Mail
 */
class SendingAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    private Collection $collectionAlert;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Collection $collectionAlert)
    {
        $this->collectionAlert = $collectionAlert;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SendingAlertMail
    {
        return $this->subject('Заканчиваются промокоды !')
            ->view('emails.alert')
            ->with(['collectionAlert' => $this->collectionAlert])
        ;
    }
}
