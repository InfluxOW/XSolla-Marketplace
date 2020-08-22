<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesRequest;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'seller']);
    }

    public function store(SalesRequest $request)
    {

    }
}
