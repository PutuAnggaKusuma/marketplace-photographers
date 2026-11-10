<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhotographerVerified
{
    /**
     * Handle an incoming request.
     * Memastikan fotografer telah melengkapi 100% profil DAN telah disetujui / di-ACC oleh Admin.
     * Sesuai instruksi: fitur operasional di-lock sebelum syarat terpenuhi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'photographer') {
            return $next($request);
        }

        $photographer = $user->rolePhotographer;
        $holdState = null;
        $message = null;

        if (!$photographer) {
            $holdState = 'incomplete';
            $message = 'Lengkapi profil fotografer Anda terlebih dahulu.';
        } elseif ($photographer->status_verifikasi === 'ditolak') {
            $holdState = 'rejected';
            $message = 'Pendaftaran akun fotografer Anda ditolak. Perbaiki profil Anda agar dapat ditinjau ulang.';
        } elseif (!$photographer->isProfileComplete()) {
            $count = count($photographer->getMissingProfileFields());
            $holdState = 'incomplete';
            $message = "Fitur belum dapat diakses. Profil Anda masih kurang {$count} data.";
        } elseif ($photographer->status_verifikasi !== 'terverifikasi') {
            $holdState = 'pending';
            $message = 'Profil Anda masih menunggu verifikasi Admin.';
        }

        if ($holdState !== null) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'hold_state' => $holdState,
                    'message' => $message,
                    'redirect' => route('photographer.profile'),
                ], 403);
            }

            return redirect()->route('photographer.profile')->with('warning', $message);
        }

        return $next($request);
    }
}

