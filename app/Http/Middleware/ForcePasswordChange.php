<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // If the superadmin is currently impersonating/simulating a user, bypass the password change redirect.
            $isImpersonating = session()->has('original_superadmin_id');

            if ($user->must_change_password && !$isImpersonating) {
                if (!$request->routeIs('password.change-form') && !$request->routeIs('password.change') && !$request->routeIs('logout')) {
                    return redirect()->route('password.change-form')->with([
                        'system-type' => 'alert-warning',
                        'system-message' => 'Please change your temporary password before proceeding.',
                    ]);
                }
            }
        }

        return $next($request);
    }
}
