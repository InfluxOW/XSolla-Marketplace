<?php

namespace App\Http\Controllers\API;

use App\Game;
use App\Helpers\Billing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'buyer']);
    }

    public function store(Request $request, Game $game, $distributor)
    {
        $key = $game->getFirstAvailableKeyAtDistributor($distributor);

        return Billing::generatePaymentToken($key, Auth::user()->email, $game->price);
    }
}
