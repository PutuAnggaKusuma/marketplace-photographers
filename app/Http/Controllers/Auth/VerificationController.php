<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Tampilkan Halaman Verifikasi Email Klien.
     */
    public function notice(Request $request)
    {
        $user = Auth::user();

        // Jika email sudah terverifikasi, langsung arahkan ke beranda atau dashboard
        if ($user->email_verified_at) {
            return redirect('/')->with('info', 'Alamat email Anda sudah terverifikasi.');
        }

        return view('public.auth.verify-email', [
            'user' => $user,
        ]);
    }

    /**
     * Kirim Ulang Tautan Verifikasi Email.
     */
    public function send(Request $request)
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect('/')->with('info', 'Alamat email Anda sudah terverifikasi.');
        }

        return back()->with('status', 'Tautan verifikasi telah dikirimkan ke alamat email Anda.');
    }

    /**
     * Klien Mengajukan Permintaan Bantuan Aktivasi Akun Manual ke Admin.
     */
    public function requestActivation(Request $request)
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect('/')->with('info', 'Alamat email Anda sudah terverifikasi.');
        }

        $request->validate([
            'pesan' => ['nullable', 'string', 'max:500'],
        ]);

        $note = trim((string) $request->input('pesan', ''));
        if (empty($note)) {
            $note = 'Email aktivasi tidak masuk ke inbox saya.';
        }

        $user->update([
            'activation_requested_at' => now(),
            'activation_request_note' => $note,
        ]);

        // Kirim Notifikasi Sistem ke seluruh Super Admin dan Admin
        $admins = User::whereIn('role', ['super_admin', 'admin'])->get();
        foreach ($admins as $admin) {
            Notification::send(
                $admin->id,
                'Bantuan Aktivasi Akun',
                "Permintaan Bantuan: {$user->nama} ({$user->email}) meminta verifikasi akun manual.",
                'activation_request',
                route('admin.users')
            );
        }

        return back()->with('success', 'Permintaan bantuan aktivasi berhasil dikirim ke admin.');
    }

    /**
     * Verifikasi Email via Tautan (Opsional).
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Tautan verifikasi email tidak valid.');
        }

        if (!$user->email_verified_at) {
            $user->update([
                'email_verified_at' => now(),
                'activation_requested_at' => null,
                'activation_request_note' => null,
            ]);
        }

        return redirect('/')->with('success', 'Email Anda berhasil diverifikasi. Selamat datang di LensMatch!');
    }
}
