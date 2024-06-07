<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckIfBlocked
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->banned_until) {
            $bannedUntil = Auth::user()->banned_until;
            $now = Carbon::now();

            if ($now->lessThan($bannedUntil)) {
                Auth::logout();

                $message = 'Your account has been suspended until ' . $bannedUntil->format('Y-m-d H:i:s');
                return response()->json(['message' => $message], 403);
            }
        }

        return $next($request);
    }
}


