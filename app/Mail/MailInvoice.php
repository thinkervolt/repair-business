<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \Illuminate\Support\Facades\Lang;


class MailInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $mail_data;
 
    public function __construct($mail_data)
    {
        $this->mail_data = $mail_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('emails.invoice')->subject(Lang::get('repair-business.email_invoice-subject'))->attach(public_path().'/invoice-receipt.pdf');
        
    }
}
