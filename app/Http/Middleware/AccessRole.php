<?php

namespace App\Http\Middleware;

use Closure;

class AccessRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->user()->hasRole($roles)) {
            return redirect()->route('admin.no-access');
        }

        return $next($request);
    }
}
