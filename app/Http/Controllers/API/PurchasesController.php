<?php

namespace App\Http\Controllers\API;

use App\Exceptions\NoAvailableKeysException;
use App\Game;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'buyer']);
    }

    public function store(Request $request, Game $game, $distributor)
    {
        try {
            $key = $game->getFirstAvailableKeyAtDistributor($distributor);
        } catch (NoAvailableKeysException $e) {
            return response($e->getMessage(), 422);
        }
        $purchase = $request->user()->reserve($key);

        return [
            'message' => "You have successfully reserved a key for the game {$game->name} at {$purchase->key->distributor->name}. To initialize payment send HTTP POST request with your card credentials and payment session token to the billing provider.",
            'payment_session_token' => $purchase['payment_session_token']
        ];
    }
}
