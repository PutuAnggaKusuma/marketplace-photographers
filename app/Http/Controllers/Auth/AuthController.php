<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Tampilkan Halaman Form Login.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }

        return view('public.auth.signin');
    }

    /**
     * Proses Login Pengguna & Automatic Role Redirection.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectBasedOnRole($user)
                ->with('success', 'Selamat datang kembali, ' . $user->nama . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan Halaman Form Registrasi Akun Baru.
     */
    public function showRegisterForm(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }

        return view('public.auth.signup');
    }

    /**
     * Proses Pendaftaran Akun Baru (Client atau Fotografer).
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:client,photographer'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem. Silakan gunakan email lain atau masuk ke akun Anda.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'role.required' => 'Pilihan role akun wajib dipilih.',
            'role.in' => 'Pilihan role akun tidak valid.',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_protected' => false,
        ]);

        Auth::login($user);

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Akun Anda berhasil dibuat. Selamat datang di LensMatch!');
    }

    /**
     * Keluar dari Akun (Logout).
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            // Update status user langsung menjadi offline saat logout (misal 5 menit lalu)
            Auth::user()->update([
                'last_seen_at' => now()->subMinutes(5)
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil keluar dari akun.');
    }

    /**
     * Helper Redirection Berdasarkan Role User.
     */
    protected function redirectBasedOnRole(User $user)
    {
        if (session()->has('url.intended')) {
            $intended = session()->pull('url.intended');
            return redirect()->to($intended);
        }

        if (in_array($user->role, ['super_admin', 'admin'])) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'photographer') {
            return redirect()->route('photographer.dashboard');
        } else {
            return redirect('/');
        }
    }
}
