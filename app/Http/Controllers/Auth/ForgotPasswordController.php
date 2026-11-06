<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan formulir permintaan reset link kata sandi.
     */
    public function showLinkRequestForm()
    {
        return view('public.auth.forgot-password');
    }

    /**
     * Proses pengiriman token reset kata sandi ke email pengguna.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Alamat email tidak ditemukan dalam sistem LensMatch.',
        ]);

        $email = $request->input('email');
        $token = Str::random(64);

        // Simpan / update token reset kata sandi
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = url("/reset-password/{$token}?email=" . urlencode($email));

        // Buat notifikasi in-app untuk pengguna
        $user = User::where('email', $email)->first();
        if ($user) {
            \App\Models\Notification::send(
                $user->id,
                'Permintaan Reset Kata Sandi',
                'Tautan pemulihan kata sandi telah dibuat. Klik notifikasi ini untuk membuat kata sandi baru Anda.',
                'security',
                $resetUrl
            );
        }

        // Tampilkan pesan sukses beserta simulasi direct link (memudahkan pengujian offline tanpa email SMTP)
        return back()->with([
            'status' => 'Tautan pemulihan kata sandi telah berhasil dikirimkan ke email Anda!',
            'simulated_reset_url' => $resetUrl,
        ]);
    }

    /**
     * Tampilkan formulir pembuatan kata sandi baru dengan token.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('public.auth.reset-password', compact('token', 'email'));
    }

    /**
     * Proses perbaharuan kata sandi baru berdasarkan token.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.exists' => 'Akun dengan email ini tidak terdaftar.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $email = $request->input('email');
        $token = $request->input('token');

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return back()->withErrors(['email' => 'Token pemulihan kata sandi tidak valid atau sudah kadaluarsa. Silakan ajukan ulang link baru.']);
        }

        // Cek kadaluarsa token (maksimal 60 menit)
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'Token pemulihan kata sandi telah kadaluarsa. Silakan minta tautan baru.']);
        }

        // Update password pengguna
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);

            \App\Models\Notification::send(
                $user->id,
                'Kata Sandi Berhasil Diperbaharui',
                'Kata sandi akun LensMatch Anda baru saja diperbaharui. Jika ini bukan Anda, segera hubungi admin sistem.',
                'security',
                url('/login')
            );
        }

        // Hapus token yang sudah digunakan
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbaharui! Silakan masuk dengan kata sandi baru.');
    }
}
