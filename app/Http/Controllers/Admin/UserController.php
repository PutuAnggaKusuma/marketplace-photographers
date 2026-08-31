<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contract;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan Tabel Pengelolaan Akun Klien (Penyewa Jasa).
     */
        public function index(Request $request)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'latest');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        // Khusus Akun Klien Saja
        $query = User::where('role', 'client');

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
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate($perPage)->withQueryString();

        // Summary Metric Stats Khusus Klien
        $totalClients = User::where('role', 'client')->count();
        $totalVerifiedClients = User::where('role', 'client')->whereNotNull('email_verified_at')->count();
        $totalBookings = Contract::count();
        $totalSpending = Contract::sum('jumlah');

        return view('admin.users.index', compact(
            'users',
            'search',
            'sort',
            'totalClients',
            'totalVerifiedClients',
            'totalBookings',
            'totalSpending'
        ));
    }

    public function destroy($id)
    {
        $user = User::where('role', 'client')->findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', "Akun klien '{$user->nama}' berhasil dihapus.");
    }
}