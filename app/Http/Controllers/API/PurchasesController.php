<?php

namespace App\Http\Controllers\API;

use App\Game;
use App\Helpers\Billing;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchasesRequest;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'buyer']);
    }

    public function store(PurchasesRequest $request, Game $game)
    {
        $key = $game->getFirstAvailableKeyAtDistributor($request->distributor);
        $session = Billing::generatePaymentSession($key, $request->user(), $game->price);

        return 'done';
    }
}
