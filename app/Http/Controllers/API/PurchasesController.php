<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchasesRequest;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'buyer']);
    }

    public function store(PurchasesRequest $request)
    {

    }
}
