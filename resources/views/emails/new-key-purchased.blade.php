@component('mail::message')
    # You have a new key!

    Hi, {{ $purchase->payer->name }}!

    You have new game {{ $purchase->key->game->name }} in your {{ $purchase->key->distributor->name }} library... Here is your key!

    {{ $purchase->key->serial_number }}

    Enjoy it,
    {{ config('app.name') }}
@endcomponent
