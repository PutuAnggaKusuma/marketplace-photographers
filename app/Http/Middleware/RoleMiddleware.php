<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        $user = Auth::user();

        // Check if user's role is in the allowed roles list
        if (!in_array($user->role, $roles)) {
            // Redirect user safely to their designated area
            if (in_array($user->role, ['super_admin', 'admin'])) {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak! Anda hanya berhak mengakses area Admin.');
            } elseif ($user->role === 'photographer') {
                return redirect()->route('photographer.dashboard')->with('error', 'Akses ditolak! Anda hanya berhak mengakses area Fotografer.');
            } else {
                return redirect('/')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengakses area tersebut.');
            }
        }

        return $next($request);
    }
}
