<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        if (Auth::guard('admin')->check()){
            if((Auth::guard('admin')->user()->status == 1)){
                
                return $next($request);
            }
            else{
                auth()->guard('admin')->logout();
                $request->session()->invalidate();
                
            }
        }
        return redirect()->route('admin.auth.login');
    }
}
