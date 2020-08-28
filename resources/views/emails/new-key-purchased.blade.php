@component('mail::message')
    # You have a new key!

    Hi {{ $purchase->buyer->name }}!

    You have new key purchased... Here it is!

    {{ $purchase->key->serial_number }}

    Enjoy the new game,
    {{ config('app.name') }}
@endcomponent
