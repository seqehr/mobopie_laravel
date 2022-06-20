<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerifcationCode extends Mailable
{
    use Queueable, SerializesModels;


    public $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Verifcation Code DreamRam')->view('emails.Verifcation',  ['details' => $this->details])->from($address = 'noreply@DreamRam.com', $name = 'Dream Ram');
    }
}
