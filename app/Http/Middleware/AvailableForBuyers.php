<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AvailableForBuyers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->isBuyer()) {
            return $next($request);
        }

        return response("You don't have permission to access this endpoint! Use your seller account instead.", 403);
    }
}
