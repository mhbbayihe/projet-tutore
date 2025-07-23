<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Auth::check()){
            return redirect()->route('login');
        }

        $user = Auth::user();

        if($user->role !== 'users'){
            abort(403, 'Access Denied. You do not have Users permissions.');
        }

        if($user->status !== 1){
            abort(403, 'Compt banner.');
        }
        
        return $next($request);
    }
}
