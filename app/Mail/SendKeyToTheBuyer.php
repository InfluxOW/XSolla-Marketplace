<?php

namespace App\Mail;

use App\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendKeyToTheBuyer extends Mailable
{
    use Queueable, SerializesModels;

    public Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function build()
    {
        return $this
            ->from('admin@marketplace.xsolla')
            ->subject("You have new key purchased!")
            ->markdown('emails.new-key-purchased');
    }
}
