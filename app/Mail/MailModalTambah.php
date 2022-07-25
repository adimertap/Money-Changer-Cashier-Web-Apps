<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailModalTambah extends Mailable
{
    public $item;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@ptriastavalasindo.com')
        ->subject('Pengajuan Penambahan Modal Hari Ini')
        ->markdown('SendEmailTambah', [
            'url' => 'ptriastavalasindo.com',
            'data' => $this->item,
        ]);
    }
}
