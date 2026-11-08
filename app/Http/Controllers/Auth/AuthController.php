<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RoleClient;
use App\Models\RolePhotographer;
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

        if ($request->filled('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        } else {
            // Bersihkan residu session lama jika user membuka /login secara mandiri (misal dari Navbar Beranda)
            session()->forget('url.intended');
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

        if ($request->filled('redirect')) {
            session(['url.intended' => $request->input('redirect')]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $intended = session('url.intended') ?: $request->input('redirect');
            $request->session()->regenerate();
            if ($intended) {
                session(['url.intended' => $intended]);
            }

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

        if ($request->filled('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        } else {
            // Bersihkan residu session lama jika user membuka /register secara mandiri
            session()->forget('url.intended');
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
            'nomor_telepon' => ['required', 'string', 'regex:/^[0-9+\-\s()]+$/', 'min:9', 'max:20'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem. Silakan gunakan email lain atau masuk ke akun Anda.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'role.required' => 'Pilihan role akun wajib dipilih.',
            'role.in' => 'Pilihan role akun tidak valid.',
            'nomor_telepon.required' => 'Nomor WhatsApp wajib diisi.',
            'nomor_telepon.regex' => 'Format nomor WhatsApp tidak valid.',
            'nomor_telepon.min' => 'Nomor WhatsApp minimal 9 digit.',
            'nomor_telepon.max' => 'Nomor WhatsApp maksimal 20 digit.',
        ]);

        if ($request->filled('redirect')) {
            session(['url.intended' => $request->input('redirect')]);
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_protected' => false,
        ]);

        // Auto-provisioning profil role sesuai pilihan pendaftaran
        if ($user->role === 'photographer') {
            RolePhotographer::create([
                'id_user' => $user->id,
                'nama' => $user->nama,
                'nomor_telepon' => $request->nomor_telepon,
                'is_onboarded' => false,
            ]);
        } elseif ($user->role === 'client') {
            RoleClient::create([
                'id_user' => $user->id,
                'nama' => $user->nama,
                'nomor_telepon' => $request->nomor_telepon,
            ]);
        }

        Auth::login($user);
        $intended = session('url.intended') ?: $request->input('redirect');
        $request->session()->regenerate();
        if ($intended) {
            session(['url.intended' => $intended]);
        }

        // Khusus fotografer baru mendaftar: langsung diarahkan ke halaman profil
        if ($user->role === 'photographer') {
            session()->forget('url.intended');
            return redirect()->route('photographer.profile')
                ->with('success', 'Akun Anda berhasil dibuat. Silakan lengkapi profil Anda.');
        }

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

            // Hindari looping kembali ke halaman login atau register
            $authPaths = [url('/login'), url('/register'), route('login'), route('register')];
            if (!empty($intended) && !in_array($intended, $authPaths)) {
                // If intended is generic /dashboard, let role dispatch it directly
                $parsedPath = parse_url($intended, PHP_URL_PATH) ?? $intended;
                if ($parsedPath === '/dashboard' || $parsedPath === 'dashboard') {
                    $intended = null;
                } else {
                    // Validasi apakah intended URL sesuai dengan role pengguna saat ini
                    $isAllowed = true;
                    if (in_array($user->role, ['super_admin', 'admin'])) {
                        if (str_contains($intended, '/photographer') || 
                            str_contains($intended, '/client') || 
                            str_contains($intended, '/booking') || 
                            str_contains($intended, '/pembayaran')) {
                            $isAllowed = false;
                        }
                    } elseif ($user->role === 'photographer') {
                        if (str_contains($intended, '/admin') || 
                            str_contains($intended, '/client') || 
                            str_contains($intended, '/booking') || 
                            str_contains($intended, '/pembayaran')) {
                            $isAllowed = false;
                        }
                    } elseif ($user->role === 'client') {
                        if (str_contains($intended, '/admin') || 
                            str_contains($intended, '/photographer')) {
                            $isAllowed = false;
                        }
                    }

                    if ($isAllowed) {
                        return redirect()->to($intended);
                    }
                }
            }
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
