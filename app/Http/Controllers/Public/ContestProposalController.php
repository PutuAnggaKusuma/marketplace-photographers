<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notification;
use App\Models\PhotoContest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContestProposalController extends Controller
{
    /**
     * Tampilkan formulir pengajuan lomba foto baru mandiri.
     */
    public function create()
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('public.contests.propose', compact('categories'));
    }

    /**
     * Simpan pengajuan lomba foto baru dengan status pending_review.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'judul_lomba' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'penyelenggara' => 'required|string|max:255',
            'hadiah' => 'required|string|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'deskrpisi_lomba' => 'required|string|min:30',
            'syarat_ketentuan' => 'required|string|min:30',
            'banner_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'banner_url' => 'nullable|url',
        ], [
            'judul_lomba.required' => 'Judul lomba wajib diisi.',
            'kategori.required' => 'Pilih salah satu kategori lomba.',
            'penyelenggara.required' => 'Nama penyelenggara/studio/komunitas wajib diisi.',
            'hadiah.required' => 'Informasi total hadiah wajib dicantumkan.',
            'start_date.required' => 'Tanggal mulai lomba wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai minimal hari ini.',
            'end_date.required' => 'Tanggal batas akhir lomba wajib diisi.',
            'end_date.after' => 'Tanggal batas akhir harus setelah tanggal mulai.',
            'deskrpisi_lomba.required' => 'Deskripsi konsep dan tema lomba wajib diisi.',
            'deskrpisi_lomba.min' => 'Deskripsi lomba minimal 30 karakter agar informatif.',
            'syarat_ketentuan.required' => 'Syarat & ketentuan teknis lomba wajib dicantumkan.',
            'syarat_ketentuan.min' => 'Syarat & ketentuan minimal 30 karakter.',
            'banner_file.max' => 'Ukuran file banner maksimal 5MB.',
        ]);

        $bannerUrl = $request->banner_url;
        if ($request->hasFile('banner_file')) {
            $path = $request->file('banner_file')->store('contest_banners', 'public');
            $bannerUrl = asset('storage/' . $path);
        }

        if (!$bannerUrl) {
            $bannerUrl = 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=1200&q=80';
        }

        $adminRole = DB::table('role_admins')->first();
        $adminId = $adminRole ? $adminRole->id : 1;

        $contest = PhotoContest::create([
            'id_admin' => $adminId,
            'user_id' => $user->id,
            'judul_lomba' => $request->judul_lomba,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'hadiah' => $request->hadiah,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending_review',
            'banner_url' => $bannerUrl,
            'deskrpisi_lomba' => $request->deskrpisi_lomba,
            'syarat_ketentuan' => $request->syarat_ketentuan,
            'catatan_admin' => null,
            'view_count' => 0,
        ]);

        // Kirim notifikasi sistem ke admin jika ada
        $creatorName = $user->nama ?? $user->name ?? 'Pengguna';
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $adm) {
            Notification::create([
                'id_user' => $adm->id,
                'type' => 'contest_proposal',
                'title' => 'Pengajuan Lomba Foto Baru',
                'message' => "Pengguna {$creatorName} mengajukan event lomba baru: '{$contest->judul_lomba}'. Silakan review pengajuan ini.",
                'data' => [
                    'contest_id' => $contest->id,
                    'url' => route('admin.contests'),
                ],
                'is_read' => false,
            ]);
        }

        return redirect()->route('public.contests.propose.history')->with('success', 'Pengajuan event lomba foto berhasil dikirim! Menunggu proses review & verifikasi oleh Tim Admin.');
    }

    /**
     * Tampilkan riwayat lomba foto yang diajukan oleh pengguna yang sedang login.
     */
    public function myProposals()
    {
        $user = Auth::user();

        $proposals = PhotoContest::withCount('submissions')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('public.contests.my-proposals', compact('proposals'));
    }
}
