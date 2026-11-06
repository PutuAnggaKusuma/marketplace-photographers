<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminStaffController extends Controller
{
    /**
     * Tampilkan Halaman Form Tambah Admin/Staff Baru.
     */
    public function create()
    {
        $this->authorizeSuperAdmin();

        return view('admin.admins.create');
    }

    /**
     * Pastikan hanya Super Admin yang dapat mengakses controller ini.
     */
    private function authorizeSuperAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            abort(403, 'Akses Ditolak. Halaman Manajemen Admin hanya dapat diakses oleh Super Admin.');
        }
    }

    /**
     * Tampilkan Halaman Manajemen Admin.
     */
        public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $search = $request->query('search', '');
        $roleFilter = $request->query('role', 'all');
        $sort = $request->query('sort', 'latest');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        $query = User::whereIn('role', ['super_admin', 'admin']);

        if ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('nama', 'asc');
        } else {
            $query->orderBy('is_protected', 'desc')->orderBy('created_at', 'desc');
        }

        $admins = $query->paginate($perPage)->withQueryString();

        // Summary Metric Stats
        $totalAdmins = User::whereIn('role', ['super_admin', 'admin'])->count();
        $totalSuperAdmins = User::where('role', 'super_admin')->count();
        $totalStaffAdmins = User::where('role', 'admin')->count();
        $verifiedAdmins = User::whereIn('role', ['super_admin', 'admin'])->whereNotNull('email_verified_at')->count();

        $role = $roleFilter;
        return view('admin.admins.index', compact(
            'admins',
            'search',
            'role',
            'roleFilter',
            'sort',
            'totalAdmins',
            'totalSuperAdmins',
            'totalStaffAdmins',
            'verifiedAdmins'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        if (!$request->has('nama') && $request->has('name')) {
            $request->merge(['nama' => $request->name]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,super_admin',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_protected' => ($validated['role'] === 'super_admin'),
            'email_verified_at' => now(),
        ]);

        RoleAdmin::create([
            'id_user' => $user->id,
            'nama' => $user->nama,
            'no_telepon' => $validated['no_hp'] ?? null,
        ]);

        return redirect()->back()->with('success', "Akun pengelola '{$validated['nama']}' ({$validated['role']}) berhasil ditambahkan.");
    }

    /**
     * Hapus Staf Admin.
     */
    public function destroy($id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);

        // Jangan izinkan menghapus diri sendiri
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Jangan izinkan menghapus akun terlindungi
        if ($user->is_protected || $user->role === 'super_admin') {
            return redirect()->back()->with('error', 'Akun Super Admin terlindungi dan tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->back()->with('success', "Staf Admin '{$user->nama}' berhasil dihapus.");
    }
}