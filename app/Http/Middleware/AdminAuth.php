<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access this page.');
        }

        if (!Auth::user()->isAdmin() && !Auth::user()->isStaff()) {
            return redirect()->route('admin.login')->with('error', 'Access denied. Admin or Staff privileges required.');
        }

        return $next($request);
    }
}
