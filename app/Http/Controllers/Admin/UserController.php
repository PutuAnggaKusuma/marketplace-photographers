<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan Tabel Pengelolaan Pengguna & Verifikasi Studio Admin.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $roleFilter = $request->query('role', 'all');

        $query = User::with('rolePhotographer')->orderBy('created_at', 'desc');

        if ($roleFilter && $roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        $totalUsers = User::count();
        $totalClients = User::where('role', 'client')->count();
        $totalPhotographers = User::where('role', 'photographer')->count();
        $totalVerifiedPhotographers = RolePhotographer::where('is_verified', true)->count();

        return view('admin.users.index', compact(
            'users', 'search', 'roleFilter', 
            'totalUsers', 'totalClients', 'totalPhotographers', 'totalVerifiedPhotographers'
        ));
    }

    /**
     * Toggle Verifikasi Centang Biru Studio Fotografer.
     */
    public function toggleVerification(Request $request, $id)
    {
        $user = User::where('role', 'photographer')->findOrFail($id);
        
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            return redirect()->back()->with('error', 'Profil studio fotografer belum ditemukan.');
        }

        $newStatus = !$photographer->is_verified;
        $photographer->update(['is_verified' => $newStatus]);

        $statusText = $newStatus ? 'diverifikasi (Centang Biru Aktif)' : 'dibatalkan verifikasinya';

        return redirect()->back()->with('success', "Status studio '{$user->nama}' berhasil {$statusText}.");
    }

    /**
     * Hapus / Nonaktifkan Pengguna.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_protected || in_array($user->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', 'Akun Administrator tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->back()->with('success', "Akun pengguna '{$user->nama}' berhasil dihapus.");
    }
}