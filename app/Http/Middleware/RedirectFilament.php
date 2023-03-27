<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Filament\Models\Contracts\FilamentUser;
use Symfony\Component\HttpFoundation\Response;

class RedirectFilament
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user instanceof FilamentUser && $user instanceof MustVerifyEmail && auth()->id() != 1) {
            if (!$user->canAccessFilament() && !$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
        }
        return $next($request);
    }
}
