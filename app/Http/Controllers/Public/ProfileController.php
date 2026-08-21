<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RolePhotographer;
use App\Models\RoleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan Halaman Pengaturan Profil & Keamanan Akun.
     */
    public function index()
    {
        $user = Auth::user();
        $photographer = $user->role === 'photographer' 
            ? RolePhotographer::where('id_user', $user->id)->first() 
            : null;

        $client = $user->role === 'client'
            ? RoleClient::where('id_user', $user->id)->first()
            : null;

        return view('public.profile.index', compact('user', 'photographer', 'client'));
    }

    /**
     * Perbarui Informasi Profil Biodata.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'foto' => 'nullable|url',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
            'deskripsi_bio' => 'nullable|string|max:1000',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah digunakan oleh pengguna lain.',
            'foto.url' => 'Format URL foto avatar tidak valid.',
        ]);

        $user->update([
            'nama' => trim($request->nama),
            'email' => trim($request->email),
        ]);

        if ($user->role === 'photographer') {
            $photographer = RolePhotographer::where('id_user', $user->id)->first();
            if ($photographer) {
                $photographer->update([
                    'nama' => trim($request->nama),
                    'foto' => trim($request->foto) ?: $photographer->foto,
                    'nomor_telepon' => trim($request->nomor_telepon),
                    'alamat' => trim($request->alamat),
                    'deskripsi_bio' => trim($request->deskripsi_bio),
                ]);
            }
        } elseif ($user->role === 'client') {
            $client = RoleClient::where('id_user', $user->id)->first();
            if ($client) {
                $client->update([
                    'nama' => trim($request->nama),
                    'foto' => trim($request->foto) ?: $client->foto,
                    'nomor_telepon' => trim($request->nomor_telepon),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Profil akun Anda berhasil diperbarui!');
    }

    /**
     * Perbarui Kata Sandi / Password Akun.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Masukkan kata sandi saat ini.',
            'new_password.required' => 'Masukkan kata sandi baru.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Kata sandi saat ini yang Anda masukkan salah.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Kata sandi akun Anda berhasil diperbarui!');
    }
}