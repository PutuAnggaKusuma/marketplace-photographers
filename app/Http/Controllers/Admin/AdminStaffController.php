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
                  ->orWhereHas('roleAdmin', function ($sq) use ($search) {
                      $sq->where('no_telepon', 'like', "%{$search}%");
                  });
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
            'nama' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,super_admin',
        ], [
            'nama.required' => 'Nama lengkap admin wajib diisi.',
            'nama.min' => 'Nama lengkap minimal 3 karakter.',
            'email.required' => 'Alamat email login wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan oleh akun lain.',
            'role.required' => 'Pilih role pengelola akun.',
            'role.in' => 'Role pengelola tidak valid.',
            'password.required' => 'Kata sandi akun wajib diisi.',
            'password.min' => 'Kata sandi akun minimal 8 karakter.',
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

        return redirect()->route('admin.admins')->with('success', "Akun pengelola '{$validated['nama']}' ({$validated['role']}) berhasil ditambahkan.");
    }

    /**
     * Tampilkan Detail Akun Pengelola / Admin.
     */
    public function show($id)
    {
        $this->authorizeSuperAdmin();

        $admin = User::whereIn('role', ['super_admin', 'admin'])
            ->with('roleAdmin')
            ->findOrFail($id);

        $activities = \App\Models\ActivityLog::where('id_user', $admin->id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.admins.show', compact('admin', 'activities'));
    }

    /**
     * Tampilkan Form Edit Akun Pengelola / Admin.
     */
    public function edit($id)
    {
        $this->authorizeSuperAdmin();

        $admin = User::whereIn('role', ['super_admin', 'admin'])
            ->with('roleAdmin')
            ->findOrFail($id);

        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Perbarui Data Akun Pengelola / Admin.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::whereIn('role', ['super_admin', 'admin'])
            ->with('roleAdmin')
            ->findOrFail($id);

        if (!$request->has('nama') && $request->has('name')) {
            $request->merge(['nama' => $request->name]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'role' => 'required|in:admin,super_admin',
            'password' => 'nullable|string|min:8',
        ], [
            'nama.required' => 'Nama lengkap admin wajib diisi.',
            'nama.min' => 'Nama lengkap minimal 3 karakter.',
            'email.required' => 'Alamat email login wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan oleh akun lain.',
            'role.required' => 'Pilih role pengelola akun.',
            'role.in' => 'Role pengelola tidak valid.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        // Proteksi: Super Admin tidak boleh downgrade akun sendiri menjadi Admin biasa
        if (Auth::id() === $user->id && $validated['role'] !== 'super_admin') {
            return redirect()->back()->withInput()->with('error', 'Anda tidak dapat menurunkan role akun Anda sendiri.');
        }

        // Proteksi: Akun root terlindungi tidak boleh diubah role-nya
        if ($user->is_protected && $validated['role'] !== 'super_admin') {
            return redirect()->back()->withInput()->with('error', 'Akun Super Admin terlindungi tidak dapat diubah rolenya.');
        }

        $userData = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_protected' => ($user->is_protected || $validated['role'] === 'super_admin'),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        if ($user->roleAdmin) {
            $user->roleAdmin->update([
                'nama' => $user->nama,
                'no_telepon' => $validated['no_hp'] ?? null,
            ]);
        } else {
            RoleAdmin::create([
                'id_user' => $user->id,
                'nama' => $user->nama,
                'no_telepon' => $validated['no_hp'] ?? null,
            ]);
        }

        return redirect()->route('admin.admins')->with('success', "Data akun pengelola '{$user->nama}' berhasil diperbaharui.");
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

        $user->roleAdmin?->delete();
        $user->delete();

        return redirect()->back()->with('success', "Staf Admin '{$user->nama}' berhasil dihapus.");
    }
}