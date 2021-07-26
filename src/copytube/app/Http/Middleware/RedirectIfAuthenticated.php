<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        //$guards = empty($guards) ? ["web"] : $guards;

        // foreach ($guards as $guard) {
        //     var_dump($guards);
        //     if (Auth::guard($guard)->check()) {
        //         return redirect('/home');
        //     }
        // }

        if (Auth::user()) {
            return response()->redirectTo("/home");
        }

        // return $next($request);
    }
}
