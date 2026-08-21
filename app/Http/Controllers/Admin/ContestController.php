<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoContest;
use App\Models\ContestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContestController extends Controller
{
    /**
     * Tampilkan Tabel Pengelolaan Lomba Foto Admin.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', 'all');

        $query = PhotoContest::withCount('submissions')->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_lomba', 'like', "%{$search}%")
                  ->orWhere('penyelenggara', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $contests = $query->paginate(10)->withQueryString();
        $totalContests = PhotoContest::count();
        $totalSubmissions = ContestSubmission::count();

        return view('admin.contests.index', compact('contests', 'search', 'status', 'totalContests', 'totalSubmissions'));
    }

    /**
     * Simpan Event Lomba Foto Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_lomba' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'penyelenggara' => 'required|string|max:255',
            'hadiah' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:buka,ditutup,selesai',
            'banner_url' => 'nullable|url',
            'deskrpisi_lomba' => 'required|string',
        ]);

        $adminRole = DB::table('role_admins')->first();

        PhotoContest::create([
            'id_admin' => $adminRole ? $adminRole->id : 1,
            'judul_lomba' => $request->judul_lomba,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'hadiah' => $request->hadiah,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'banner_url' => $request->banner_url ?: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&q=80',
            'deskrpisi_lomba' => $request->deskrpisi_lomba,
        ]);

        return redirect()->back()->with('success', 'Event lomba foto baru berhasil diterbitkan!');
    }

    /**
     * Perbarui Event Lomba Foto.
     */
    public function update(Request $request, $id)
    {
        $contest = PhotoContest::findOrFail($id);

        $request->validate([
            'judul_lomba' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'penyelenggara' => 'required|string|max:255',
            'hadiah' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:buka,ditutup,selesai',
            'banner_url' => 'nullable|url',
            'deskrpisi_lomba' => 'required|string',
        ]);

        $contest->update([
            'judul_lomba' => $request->judul_lomba,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'hadiah' => $request->hadiah,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'banner_url' => $request->banner_url ?: $contest->banner_url,
            'deskrpisi_lomba' => $request->deskrpisi_lomba,
        ]);

        return redirect()->back()->with('success', 'Event lomba foto berhasil diperbarui!');
    }

    /**
     * Hapus Event Lomba Foto.
     */
    public function destroy($id)
    {
        $contest = PhotoContest::findOrFail($id);
        $contest->submissions()->delete();
        $contest->delete();

        return redirect()->back()->with('success', 'Event lomba foto berhasil dihapus.');
    }

    /**
     * Galeri Karya Peserta Pendaftaran Lomba Foto.
     */
    public function submissions($id)
    {
        $contest = PhotoContest::findOrFail($id);
        $submissions = ContestSubmission::with('user')
            ->where('id_contest', $contest->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.contests.submissions', compact('contest', 'submissions'));
    }

    /**
     * Penetapan Pemenang Juara Lomba Foto (1, 2, 3).
     */
    public function setWinner(Request $request, $submissionId)
    {
        $submission = ContestSubmission::findOrFail($submissionId);

        $request->validate([
            'status_submission' => 'required|in:verified,winner_1,winner_2,winner_3,rejected',
        ]);

        $submission->update(['status_submission' => $request->status_submission]);

        return redirect()->back()->with('success', "Status juara karya '{$submission->judul_karya}' berhasil diperbarui.");
    }
}