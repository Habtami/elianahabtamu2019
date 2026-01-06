<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if (!in_array($user->role, $roles)) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to access this page.');
        }
        
        return $next($request);
    }
}