<?php

namespace App\Mail;

use App\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendKeyToThePayer extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Payment $purchase;

    public function __construct(Payment $purchase)
    {
        $this->purchase = $purchase;
    }

    public function build()
    {
        return $this
            ->subject("You have new key purchased!")
            ->markdown('emails.new-key-purchased');
    }
}
