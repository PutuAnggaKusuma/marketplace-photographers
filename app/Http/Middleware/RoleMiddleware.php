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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'message' => 'Silakan masuk ke akun Anda terlebih dahulu.',
                    'redirect' => route('login', ['redirect' => url()->previous()]),
                ], 401);
            }
            return redirect()->guest(route('login'))->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        $user = Auth::user();

        // Check if user's role is in the allowed roles list
        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson() || $request->ajax()) {
                $roleNames = [
                    'client' => 'Klien',
                    'photographer' => 'Fotografer',
                    'admin' => 'Admin',
                    'super_admin' => 'Super Admin',
                ];
                $formattedRoles = array_map(fn($r) => $roleNames[$r] ?? ucfirst($r), $roles);
                return response()->json([
                    'success' => false,
                    'message' => 'Fitur ini khusus untuk akun ' . implode('/', $formattedRoles) . '.',
                ], 403);
            }
            // Check if the request is simply trying to reach a dashboard or root admin/photographer area
            $isDashboardRequest = $request->is('admin/dashboard') || 
                                  $request->is('photographer/dashboard') || 
                                  $request->is('dashboard') || 
                                  $request->is('admin') || 
                                  $request->is('photographer') ||
                                  $request->is('client/bookings');

            // Redirect user safely to their designated area
            if (in_array($user->role, ['super_admin', 'admin'])) {
                $redirect = redirect()->route('admin.dashboard');
                return $isDashboardRequest 
                    ? $redirect 
                    : $redirect->with('error', 'Akses ditolak! Anda hanya berhak mengakses area Admin.');
            } elseif ($user->role === 'photographer') {
                $redirect = redirect()->route('photographer.dashboard');
                return $isDashboardRequest 
                    ? $redirect 
                    : $redirect->with('error', 'Akses ditolak! Anda hanya berhak mengakses area Fotografer.');
            } else {
                $redirect = redirect('/');
                return $isDashboardRequest 
                    ? $redirect 
                    : redirect('/')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengakses area tersebut.');
            }
        }

        return $next($request);
    }
}
