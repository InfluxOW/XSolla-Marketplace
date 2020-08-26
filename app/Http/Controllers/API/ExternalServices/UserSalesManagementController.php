<?php

namespace App\Http\Controllers\API\ExternalServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserSalesManagementController extends Controller
{
    /*
     * Fake user server that should receive a message from marketplace about sold key.
     * */

    public function store(Request $request)
    {
        return now()->timestamp % 2 === 0 ? response('Key sale has been processed.', 201) : response('Whops, something went wrong. Try again later.', 503);
    }
}
