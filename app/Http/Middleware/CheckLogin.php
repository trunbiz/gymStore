<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class CheckLogin
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
        if (Auth::check()) {
//            if(Auth::user()->lever==0)
//            {
//                return $next($request);
//            }
//            else{
//                return redirect()->intended('/');
//            }
            return $next($request);
        } else {
            return redirect("login");
        }
    }
}
