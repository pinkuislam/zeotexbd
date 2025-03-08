<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ValidAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (Auth::user()->status != 'Active') {
            auth('web')->logout();
            return redirect()->route('error', 'deactivated');
        }

        if (!in_array(Auth::user()->role, ['Admin', 'Seller', 'Reseller', 'Staff'])) {
            auth('web')->logout();
            return redirect()->route('error', 'invalid-role');
        }

        return $next($request);
    }
}