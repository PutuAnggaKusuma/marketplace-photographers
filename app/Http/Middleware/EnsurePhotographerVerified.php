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
        if (!$photographer) {
            return redirect()->route('photographer.profile')
                ->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }

        if (!$photographer->isProfileComplete()) {
            $missing = $photographer->getMissingProfileFields();
            $count = count($missing);
            return redirect()->route('photographer.profile')
                ->with('warning', "Fitur belum dapat diakses. Anda masih kurang {$count} data untuk melengkapi profil Anda.");
        }

        if ($photographer->status_verifikasi === 'ditolak') {
            return redirect()->route('photographer.profile')
                ->with('warning', 'Pendaftaran Anda ditolak. Silakan periksa catatan penolakan dan perbaiki profil Anda.');
        }

        if ($photographer->status_verifikasi !== 'terverifikasi') {
            return redirect()->route('photographer.profile')
                ->with('warning', 'Profil Anda telah lengkap, namun akun Anda masih menunggu proses verifikasi Admin.');
        }

        return $next($request);
    }
}

