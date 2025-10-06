<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('student')->user();
        
        // If user is not authenticated, let the auth middleware handle it
        if (!$user) {
            return $next($request);
        }
        
        // Check if student is blocked; share flags with all views and continue
        if ($user->is_blocked) {
            $currentRoute = $request->route() ? $request->route()->getName() : null;
            $allowedRoutes = [
                'student.bills',
                'student.payment',
                'student.receipt.show',
                'student.receipt.pdf',
                'student.receipt.view-pdf',
                'student.invoice.pdf',
                'student.invoice.view-pdf',
                'student.logout'
            ];

            // Share flags so blades can decide to blur/disable UI
            \View::share('isBlockedStudent', true);
            \View::share('blockedAllowedRoutes', $allowedRoutes);
            \View::share('blockedCurrentRoute', $currentRoute);
        } else {
            \View::share('isBlockedStudent', false);
        }
        
        return $next($request);
    }
}