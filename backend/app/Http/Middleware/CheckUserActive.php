<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->status !== 'active') {
            // Optionally revoke current token
            $request->user()->currentAccessToken()?->delete();

            return response()->json([
                'message' => __('messages.account_inactive'),
            ], 403);
        }

        return $next($request);
    }
}
