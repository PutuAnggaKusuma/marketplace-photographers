<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PhotoContest;
use App\Models\ContestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContestController extends Controller
{
    /**
     * Tampilkan Katalog Event Lomba Foto Publik.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $sortFilter = $request->query('sort', 'terbaru');
        $search = $request->query('q');

        $query = PhotoContest::withCount('submissions');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_lomba', 'like', "%{$search}%")
                  ->orWhere('penyelenggara', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('deskrpisi_lomba', 'like', "%{$search}%");
            });
        }

        if ($sortFilter === 'populer') {
            $query->orderBy('view_count', 'desc');
        } elseif ($sortFilter === 'deadline') {
            $query->orderBy('end_date', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $contests = $query->paginate($perPage)->withQueryString();
        $totalActiveContests = PhotoContest::where('status', 'buka')->count();

        return view('public.contests.index', compact('contests', 'statusFilter', 'sortFilter', 'search', 'totalActiveContests', 'perPage'));
    }

    /**
     * Tampilkan Detail Event Lomba & Galeri Pemenang.
     */
    public function show($id)
    {
        $contest = PhotoContest::withCount('submissions')->findOrFail($id);
        $contest->increment('view_count');

        $submissions = ContestSubmission::with('user')
            ->where('id_contest', $contest->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $winners = ContestSubmission::with('user')
            ->where('id_contest', $contest->id)
            ->whereIn('status_submission', ['winner_1', 'winner_2', 'winner_3'])
            ->orderBy('status_submission', 'asc')
            ->get();

        $userSubmission = Auth::check() 
            ? ContestSubmission::where('id_contest', $contest->id)->where('id_user', Auth::id())->first() 
            : null;

        return view('public.contests.show', compact('contest', 'submissions', 'winners', 'userSubmission'));
    }

    /**
     * Submit Karya Foto Peserta.
     */
    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengikuti lomba foto.');
        }

        $contest = PhotoContest::findOrFail($id);
        if ($contest->status !== 'buka') {
            return redirect()->back()->with('error', 'Pendaftaran untuk event lomba ini telah ditutup.');
        }

        $existing = ContestSubmission::where('id_contest', $contest->id)->where('id_user', $user->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah mendaftarkan karya foto pada event lomba ini.');
        }

        $request->validate([
            'judul_karya' => 'required|string|max:255',
            'deskripsi_karya' => 'nullable|string|max:1000',
            'image_url' => 'required|url',
        ], [
            'judul_karya.required' => 'Tuliskan judul karya foto Anda.',
            'image_url.required' => 'Sertakan URL link foto karya Anda.',
            'image_url.url' => 'Format URL link foto tidak valid.',
        ]);

        ContestSubmission::create([
            'id_contest' => $contest->id,
            'id_user' => $user->id,
            'judul_karya' => $request->judul_karya,
            'deskripsi_karya' => $request->deskripsi_karya,
            'image_url' => $request->image_url,
            'status_submission' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Karya foto Anda berhasil didaftarkan! Menunggu evaluasi juri.');
    }
}