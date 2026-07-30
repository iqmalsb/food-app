<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CheckSeatsLimit
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
            
            // Superadmins are exempt from seats limit checks
            if ($user->role === 'superadmin') {
                return $next($request);
            }

            $org = $user->organisation;
            if ($org) {
                $activeSeatsCount = User::where('organisation_id', $org->id)->count();
                
                if ($activeSeatsCount >= $org->seats_limit) {
                    if ($request->isMethod('post')) {
                        throw ValidationException::withMessages([
                            'role' => ['Your organisation has reached its user seat limit.'],
                        ]);
                    }
                    return redirect()->route('users.index')->with([
                        'alert-type' => 'alert-danger',
                        'alert-message' => 'Your organisation has reached its seats limit (' . $org->seats_limit . ' seats). You cannot create more users.',
                    ]);
                }
            }
        }

        return $next($request);
    }
}
