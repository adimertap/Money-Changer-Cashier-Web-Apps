<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailModal extends Mailable
{
    public $modal;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
    */
    public function __construct($modal)
    {
        $this->modal = $modal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@ptriastavalasindo.com')
                ->subject('Pengajuan Modal Hari Ini')
                ->markdown('SendEmail', [
                    'url' => 'ptriastavalasindo.com',
                    'data' => $this->modal,
                ]);
    }
}
