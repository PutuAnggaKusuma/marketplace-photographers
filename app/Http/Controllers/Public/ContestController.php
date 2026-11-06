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
        $categoryFilter = $request->query('kategori', 'all');
        $sortFilter = $request->query('sort', 'terbaru');
        $search = $request->query('q');

        $query = PhotoContest::withCount('submissions')
            ->whereNotIn('status', ['pending_review', 'ditolak']);

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($categoryFilter && $categoryFilter !== 'all') {
            $query->where('kategori', $categoryFilter);
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

        // Kategori lomba unik dari database
        $categories = PhotoContest::whereNotIn('status', ['pending_review', 'ditolak'])
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori');

        return view('public.contests.index', compact(
            'contests', 
            'statusFilter', 
            'categoryFilter',
            'categories',
            'sortFilter', 
            'search', 
            'totalActiveContests', 
            'perPage'
        ));
    }

    /**
     * Tampilkan Detail Event Lomba & Galeri Pemenang.
     */
    public function show($id)
    {
        $contest = PhotoContest::withCount(['submissions', 'registrations'])->findOrFail($id);

        if (in_array($contest->status, ['pending_review', 'ditolak'])) {
            if (!Auth::check() || (Auth::id() !== $contest->user_id && !in_array(Auth::user()->role, ['admin', 'super_admin']))) {
                return redirect()->route('public.contests.index')->with('error', 'Event lomba ini belum disetujui atau tidak tersedia untuk publik.');
            }
        }

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

        $userRegistration = null;
        $userSubmissions = collect();
        $profileData = null;

        if (Auth::check()) {
            $user = Auth::user();
            $userRegistration = \App\Models\ContestRegistration::where('id_contest', $contest->id)
                ->where('id_user', $user->id)
                ->first();

            $userSubmissions = ContestSubmission::where('id_contest', $contest->id)
                ->where('id_user', $user->id)
                ->get();

            // Prepare Auto-Fill Profile Data
            $phone = '';
            $city = '';
            $bio = '';
            $portfolioLink = '';

            if ($user->role === 'client') {
                $client = \App\Models\RoleClient::where('id_user', $user->id)->first();
                if ($client) {
                    $phone = $client->nomor_telepon ?? '';
                    $city = $client->alamat ?? '';
                }
            } elseif ($user->role === 'photographer') {
                $photo = \App\Models\RolePhotographer::where('id_user', $user->id)->first();
                if ($photo) {
                    $phone = $photo->nomor_telepon ?? '';
                    $city = $photo->alamat ?? '';
                    $bio = $photo->deskripsi_bio ?? '';
                    $portfolioLink = $photo->link_sosmed ?? '';
                }
            }

            $profileData = [
                'nama' => $user->nama,
                'email' => $user->email,
                'no_wa' => $phone,
                'asal_kota' => $city,
                'bio' => $bio,
                'portfolio_link' => $portfolioLink,
            ];
        }

        return view('public.contests.show', compact(
            'contest', 
            'submissions', 
            'winners', 
            'userRegistration', 
            'userSubmissions', 
            'profileData'
        ));
    }

    /**
     * Pendaftaran Calon Peserta Lomba Foto (Tahap 1).
     */
    public function registerParticipant(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login terlebih dahulu untuk mendaftar lomba.');
        }

        $contest = PhotoContest::findOrFail($id);
        if ($contest->status !== 'buka') {
            return redirect()->back()->with('error', 'Pendaftaran untuk event lomba ini tidak sedang dibuka.');
        }

        $existing = \App\Models\ContestRegistration::where('id_contest', $contest->id)
            ->where('id_user', $user->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan formulir pendaftaran untuk event lomba ini.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_wa' => 'required|string|max:30',
            'asal_kota' => 'nullable|string|max:100',
            'instansi' => 'nullable|string|max:150',
            'bio_singkat' => 'nullable|string|max:1000',
            'motivasi' => 'nullable|string|max:1000',
            'cv_portfolio_file' => 'nullable|file|mimes:pdf,zip,rar|max:10240',
            'portfolio_link' => 'nullable|string|max:255',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email aktif wajib diisi.',
            'no_wa.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'cv_portfolio_file.mimes' => 'Berkas CV/Portofolio harus berupa file PDF atau arsip ZIP/RAR.',
            'cv_portfolio_file.max' => 'Ukuran berkas CV/Portofolio maksimal 10MB.',
        ]);

        $cvFilePath = null;
        if ($request->hasFile('cv_portfolio_file')) {
            $path = $request->file('cv_portfolio_file')->store('contest_registrations_cv', 'public');
            $cvFilePath = asset('storage/' . $path);
        }

        $statusBayar = ($contest->biaya_pendaftaran > 0) ? 'menunggu_pembayaran' : 'gratis';

        \App\Models\ContestRegistration::create([
            'id_contest' => $contest->id,
            'id_user' => $user->id,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_wa' => $request->no_wa,
            'asal_kota' => $request->asal_kota,
            'instansi' => $request->instansi,
            'bio_singkat' => $request->bio_singkat,
            'motivasi' => $request->motivasi,
            'cv_portfolio_file' => $cvFilePath,
            'portfolio_link' => $request->portfolio_link,
            'status_pendaftaran' => 'menunggu_verifikasi',
            'status_pembayaran' => $statusBayar,
        ]);

        return redirect()->back()->with('success', 'Formulir pendaftaran calon peserta berhasil dikirim! Menunggu kurasi & verifikasi oleh Tim Admin.');
    }

    /**
     * Upload Bukti Pembayaran Pendaftaran Lomba (Jika Berbayar).
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $contest = PhotoContest::findOrFail($id);
        $registration = \App\Models\ContestRegistration::where('id_contest', $contest->id)
            ->where('id_user', $user->id)
            ->firstOrFail();

        if ($registration->status_pendaftaran !== 'disetujui') {
            return redirect()->back()->with('error', 'Pendaftaran Anda belum disetujui atau tidak memerlukan pembayaran saat ini.');
        }

        $request->validate([
            'bukti_transfer' => 'required|image|max:5120',
        ], [
            'bukti_transfer.required' => 'Silakan upload file foto struk/bukti transfer bank.',
            'bukti_transfer.image' => 'Bukti transfer harus berupa file gambar (JPG, PNG, JPEG).',
            'bukti_transfer.max' => 'Ukuran file bukti transfer maksimal 5MB.',
        ]);

        $path = $request->file('bukti_transfer')->store('contest_payment_proofs', 'public');
        $buktiUrl = asset('storage/' . $path);

        $registration->update([
            'bukti_transfer' => $buktiUrl,
            'status_pembayaran' => 'menunggu_konfirmasi',
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah! Admin akan segera memverifikasi transaksi Anda.');
    }

    /**
     * Submit Karya Foto Peserta (Tahap 3: Pengumpulan Karya).
     */
    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login terlebih dahulu untuk mengikuti lomba foto.');
        }

        $contest = PhotoContest::findOrFail($id);

        if ($contest->status !== 'buka' || ($contest->end_date && \Carbon\Carbon::parse($contest->end_date)->endOfDay()->isPast())) {
            return redirect()->back()->with('error', 'Batas waktu pengunggahan karya untuk lomba foto ini telah berakhir.');
        }

        $registration = \App\Models\ContestRegistration::where('id_contest', $contest->id)
            ->where('id_user', $user->id)
            ->first();

        if (!$registration || !$registration->isApprovedAndReady()) {
            return redirect()->back()->with('error', 'Anda harus memiliki pendaftaran yang telah disetujui dan lunas oleh Admin untuk dapat mengunggah karya foto.');
        }

        $maxAllowed = $contest->max_submissions ?: 1;
        $currentCount = ContestSubmission::where('id_contest', $contest->id)->where('id_user', $user->id)->count();

        if ($currentCount >= $maxAllowed) {
            return redirect()->back()->with('error', "Anda telah mencapai batas maksimal upload karya untuk lomba ini ({$maxAllowed} karya).");
        }

        $request->validate([
            'judul_karya' => 'required|string|max:255',
            'deskripsi_karya' => 'nullable|string|max:1000',
            'foto_file' => 'nullable|image|max:10240',
            'media_link' => 'nullable|string|max:500',
        ], [
            'judul_karya.required' => 'Tuliskan judul karya foto Anda.',
        ]);

        $imageUrl = null;
        if ($request->hasFile('foto_file')) {
            $path = $request->file('foto_file')->store('contest_submissions', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        if (!$imageUrl && !$request->filled('media_link')) {
            return redirect()->back()->with('error', 'Silakan upload file foto dari perangkat Anda atau masukkan tautan karya.')
                ->withInput();
        }

        ContestSubmission::create([
            'id_contest' => $contest->id,
            'id_user' => $user->id,
            'id_registration' => $registration->id,
            'judul_karya' => $request->judul_karya,
            'deskripsi_karya' => $request->deskripsi_karya,
            'image_url' => $imageUrl ?: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=800&q=80',
            'media_link' => $request->media_link,
            'status_submission' => 'verified',
        ]);

        return redirect()->back()->with('success', 'Karya foto Anda berhasil dikirimkan! Karya telah tersimpan untuk tahap penjurian dewan juri.');
    }
}