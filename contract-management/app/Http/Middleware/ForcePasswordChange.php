<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Redirect users holding a temporary password to the change-password page
     * until they choose a new one.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->must_change_password) {
            $allowed = $request->is('password/change')
                || $request->is('password/change/*')
                || $request->is('logout')
                || $request->is('logout/*');

            if (! $allowed) {
                return redirect()->route('password.change')
                    ->with('warning', 'እባክዎ መጀመሪያ ፓስዎርድዎን ይቀይሩ። Please change your password first.');
            }
        }

        return $next($request);
    }
}
