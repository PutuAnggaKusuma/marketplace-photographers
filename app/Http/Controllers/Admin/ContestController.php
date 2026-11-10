<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\PhotoContest;

use App\Models\ContestSubmission;

use App\Models\Notification;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;



class ContestController extends Controller

{

    /**

     * Tampilkan Halaman Form Tambah Lomba Foto Baru.

     */

    public function create()

    {

        return view('admin.contests.create');

    }



    /**

     * Tampilkan Halaman Form Edit Lomba Foto.

     */

    public function edit($id)

    {

        $contest = PhotoContest::with('creator')->findOrFail($id);

        return view('admin.contests.edit', compact('contest'));

    }



    /**

     * Tampilkan Tabel Pengelolaan Lomba Foto Admin.

     */

    public function index(Request $request)

    {

        $search = $request->query('search', '');

        $status = $request->query('status', 'all');

        $sort = $request->query('sort', 'latest');



        $query = PhotoContest::with(['creator.roleClient', 'creator.rolePhotographer', 'admin'])->withCount(['submissions', 'registrations']);



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



        if ($sort === 'oldest') {

            $query->orderBy('created_at', 'asc');

        } elseif ($sort === 'highest_prize') {

            $query->orderBy('hadiah', 'desc');

        } else {

            $query->orderBy('created_at', 'desc');

        }



        $perPage = (int) $request->query('per_page', 10);

        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        $contests = $query->paginate($perPage)->withQueryString();



        $totalContests = PhotoContest::count();

        $pendingContests = PhotoContest::where('status', 'pending_review')->count();

        $openContests = PhotoContest::where('status', 'buka')->count();

        $completedContests = PhotoContest::where('status', 'selesai')->count();

        $rejectedContests = PhotoContest::where('status', 'ditolak')->count();

        $totalSubmissions = ContestSubmission::count();



        $totalHadiah = PhotoContest::where('status', 'buka')->get()->sum(function ($c) {

            return (int) preg_replace('/[^0-9]/', '', $c->hadiah ?: '0');

        });



        return view('admin.contests.index', compact(

            'contests', 

            'search', 

            'status', 

            'sort', 

            'totalContests', 

            'pendingContests', 

            'openContests', 

            'completedContests', 

            'rejectedContests', 

            'totalSubmissions', 

            'totalHadiah'

        ));

    }



    /**

     * Setujui (Approve) Pengajuan Lomba Foto.

     */

    public function approve($id)

    {

        $contest = PhotoContest::findOrFail($id);

        $contest->update([

            'status' => 'buka',

            'catatan_admin' => null,

        ]);



        if ($contest->user_id) {

            Notification::create([

                'id_user' => $contest->user_id,

                'type' => 'contest_approved',

                'title' => 'Pengajuan Lomba Disetujui',

                'message' => "Selamat, event lomba '{$contest->judul_lomba}' telah disetujui Admin dan resmi dibuka di katalog publik.",

                'data' => [

                    'contest_id' => $contest->id,

                    'url' => route('public.contests.show', $contest->id),

                ],

                'is_read' => false,

            ]);

        }



        if (request()->wantsJson()) {

            return response()->json([

                'success' => true,

                'message' => "Event lomba '{$contest->judul_lomba}' berhasil disetujui & dipublikasikan!"

            ]);

        }



        return redirect()->back()->with('success', "Event lomba '{$contest->judul_lomba}' berhasil disetujui & dipublikasikan!");

    }



    /**

     * Tolak (Reject) Pengajuan Lomba Foto dengan Alasan.

     */

    public function reject(Request $request, $id)

    {

        $contest = PhotoContest::findOrFail($id);



        $request->validate([

            'catatan_admin' => 'required|string|min:5',

        ], [

            'catatan_admin.required' => 'Berikan catatan alasan mengapa pengajuan lomba ini ditolak.',

            'catatan_admin.min' => 'Catatan penolakan minimal 5 karakter.',

        ]);



        $contest->update([

            'status' => 'ditolak',

            'catatan_admin' => $request->catatan_admin,

        ]);



        if ($contest->user_id) {

            Notification::create([

                'id_user' => $contest->user_id,

                'type' => 'contest_rejected',

                'title' => 'Pengajuan Lomba Ditolak / Perlu Revisi',

                'message' => "Pengajuan lomba '{$contest->judul_lomba}' ditolak oleh Admin. Alasan: {$request->catatan_admin}",

                'data' => [

                    'contest_id' => $contest->id,

                    'url' => route('public.contests.propose.history'),

                ],

                'is_read' => false,

            ]);

        }



        if (request()->wantsJson()) {

            return response()->json([

                'success' => true,

                'message' => "Pengajuan lomba '{$contest->judul_lomba}' telah ditandai ditolak dengan catatan revisi."

            ]);

        }



        return redirect()->back()->with('success', "Pengajuan lomba '{$contest->judul_lomba}' telah ditandai ditolak dengan catatan revisi.");

    }



    public function store(Request $request)

    {

        $request->validate([

            'judul_lomba' => 'required|string|max:255',

            'kategori' => 'required|string|max:100',

            'penyelenggara' => 'required|string|max:255',

            'hadiah' => 'required|string|max:100',

            'biaya_pendaftaran' => 'nullable|numeric|min:0',

            'bank_name' => 'nullable|string|max:100',

            'bank_account_number' => 'nullable|string|max:100',

            'bank_account_holder' => 'nullable|string|max:150',

            'max_submissions' => 'required|integer|min:1|max:10',

            'allow_link' => 'nullable|boolean',

            'registration_start' => 'nullable|date',

            'registration_end' => 'nullable|date|after_or_equal:registration_start',

            'submission_start' => 'nullable|date',

            'submission_end' => 'nullable|date|after_or_equal:submission_start',

            'judging_start' => 'nullable|date',

            'judging_end' => 'nullable|date|after_or_equal:judging_start',

            'announcement_date' => 'nullable|date',

            'start_date' => 'required|date',

            'end_date' => 'required|date|after_or_equal:start_date',

            'status' => 'required|in:pending_review,buka,ditutup,selesai,ditolak',

            'banner_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',

            'banner_url' => 'nullable|url',

            'deskrpisi_lomba' => 'required|string',

            'syarat_ketentuan' => 'nullable|string',

        ], [

            'banner_file.image' => 'File poster harus berupa gambar.',

            'banner_file.mimes' => 'Format poster harus JPG, PNG, atau WEBP.',

            'banner_file.max' => 'Ukuran file poster maksimal 10 MB.',

        ]);



        $bannerUrl = $request->banner_url;

        if ($request->hasFile('banner_file')) {

            $path = $request->file('banner_file')->store('contest_banners', 'public');

            $bannerUrl = asset('storage/' . $path);

        }



        $adminRole = DB::table('role_admins')->first();



        PhotoContest::create([

            'id_admin' => $adminRole ? $adminRole->id : 1,

            'user_id' => Auth::id(),

            'judul_lomba' => $request->judul_lomba,

            'kategori' => $request->kategori,

            'penyelenggara' => $request->penyelenggara,

            'hadiah' => $request->hadiah,

            'biaya_pendaftaran' => $request->biaya_pendaftaran ?: 0,

            'bank_name' => $request->bank_name,

            'bank_account_number' => $request->bank_account_number,

            'bank_account_holder' => $request->bank_account_holder,

            'max_submissions' => $request->max_submissions ?: 1,

            'allow_link' => $request->boolean('allow_link'),

            'registration_start' => $request->registration_start ?: $request->start_date,

            'registration_end' => $request->registration_end,

            'submission_start' => $request->submission_start,

            'submission_end' => $request->submission_end,

            'judging_start' => $request->judging_start,

            'judging_end' => $request->judging_end,

            'announcement_date' => $request->announcement_date ?: $request->end_date,

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

            'status' => $request->status,

            'banner_url' => $bannerUrl ?: null,

            'deskrpisi_lomba' => $request->deskrpisi_lomba,

            'syarat_ketentuan' => $request->syarat_ketentuan,

        ]);



        return redirect()->route('admin.contests')->with('success', 'Event lomba foto baru berhasil diterbitkan!');

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

            'biaya_pendaftaran' => 'nullable|numeric|min:0',

            'bank_name' => 'nullable|string|max:100',

            'bank_account_number' => 'nullable|string|max:100',

            'bank_account_holder' => 'nullable|string|max:150',

            'max_submissions' => 'required|integer|min:1|max:10',

            'allow_link' => 'nullable|boolean',

            'registration_start' => 'nullable|date',

            'registration_end' => 'nullable|date|after_or_equal:registration_start',

            'submission_start' => 'nullable|date',

            'submission_end' => 'nullable|date|after_or_equal:submission_start',

            'judging_start' => 'nullable|date',

            'judging_end' => 'nullable|date|after_or_equal:judging_start',

            'announcement_date' => 'nullable|date',

            'start_date' => 'required|date',

            'end_date' => 'required|date|after_or_equal:start_date',

            'status' => 'required|in:pending_review,buka,ditutup,selesai,ditolak',

            'banner_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',

            'banner_url' => 'nullable|url',

            'deskrpisi_lomba' => 'required|string',

            'syarat_ketentuan' => 'nullable|string',

            'catatan_admin' => 'nullable|string',

        ], [

            'banner_file.image' => 'File poster harus berupa gambar.',

            'banner_file.mimes' => 'Format poster harus JPG, PNG, atau WEBP.',

            'banner_file.max' => 'Ukuran file poster maksimal 10 MB.',

        ]);



        $bannerUrl = $contest->banner_url;

        if ($request->hasFile('banner_file')) {

            $path = $request->file('banner_file')->store('contest_banners', 'public');

            $bannerUrl = asset('storage/' . $path);

        } elseif ($request->filled('banner_url')) {

            $bannerUrl = $request->banner_url;

        }



        $contest->update([

            'judul_lomba' => $request->judul_lomba,

            'kategori' => $request->kategori,

            'penyelenggara' => $request->penyelenggara,

            'hadiah' => $request->hadiah,

            'biaya_pendaftaran' => $request->biaya_pendaftaran ?: 0,

            'bank_name' => $request->bank_name,

            'bank_account_number' => $request->bank_account_number,

            'bank_account_holder' => $request->bank_account_holder,

            'max_submissions' => $request->max_submissions ?: 1,

            'allow_link' => $request->boolean('allow_link'),

            'registration_start' => $request->registration_start,

            'registration_end' => $request->registration_end,

            'submission_start' => $request->submission_start,

            'submission_end' => $request->submission_end,

            'judging_start' => $request->judging_start,

            'judging_end' => $request->judging_end,

            'announcement_date' => $request->announcement_date,

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

            'status' => $request->status,

            'banner_url' => $bannerUrl,

            'deskrpisi_lomba' => $request->deskrpisi_lomba,

            'syarat_ketentuan' => $request->syarat_ketentuan,

            'catatan_admin' => $request->catatan_admin,

        ]);



        return redirect()->route('admin.contests')->with('success', 'Event lomba foto berhasil diperbarui!');

    }



    /**

     * Hapus Event Lomba Foto.

     */

    public function destroy($id)

    {

        $contest = PhotoContest::findOrFail($id);

        $contest->submissions()->delete();

        $contest->registrations()->delete();

        $contest->delete();



        return redirect()->back()->with('success', 'Event lomba foto berhasil dihapus.');

    }





    /**

     * Update Status & Verifikasi Pendaftaran Peserta (Unified Form).

     */

    public function updateParticipantStatus(Request $request, $registrationId)

    {

        $registration = \App\Models\ContestRegistration::with('contest')->findOrFail($registrationId);



        $request->validate([

            'status_pendaftaran' => 'required|in:disetujui,menunggu_verifikasi,ditolak',

            'status_pembayaran' => 'nullable|in:lunas,menunggu_konfirmasi,menunggu_pembayaran,gratis',

            'catatan_admin' => 'nullable|string',

        ]);



        if ($request->status_pendaftaran === 'ditolak' && empty($request->catatan_admin)) {

            return redirect()->back()->with('error', 'Wajib mencantumkan alasan penolakan jika status pendaftaran ditolak.');

        }



        $dataToUpdate = [

            'status_pendaftaran' => $request->status_pendaftaran,

            'catatan_admin' => $request->status_pendaftaran === 'ditolak' ? $request->catatan_admin : ($request->catatan_admin ?: null),

        ];



        $oldPaymentStatus = $registration->status_pembayaran;

        if ($request->filled('status_pembayaran')) {

            $dataToUpdate['status_pembayaran'] = $request->status_pembayaran;

        }



        $registration->update($dataToUpdate);



        if ($request->status_pendaftaran === 'disetujui') {

            $message = ($registration->contest && $registration->contest->biaya_pendaftaran > 0) 

                ? "Pendaftaran Anda untuk lomba '{$registration->contest->judul_lomba}' telah DISETUJUI!"

                : "Selamat! Pendaftaran Anda untuk lomba '{$registration->contest->judul_lomba}' telah DISETUJUI dan Anda resmi terdaftar sebagai peserta.";



            Notification::create([

                'id_user' => $registration->id_user,

                'type' => 'contest_participant_approved',

                'title' => 'Pendaftaran Lomba Disetujui',

                'message' => $message,

                'data' => [

                    'contest_id' => $registration->id_contest,

                    'url' => route('public.contests.show', $registration->id_contest),

                ],

                'is_read' => false,

            ]);

        } elseif ($request->status_pendaftaran === 'ditolak') {

            Notification::create([

                'id_user' => $registration->id_user,

                'type' => 'contest_participant_rejected',

                'title' => 'Pendaftaran Lomba Tidak Lolos Kurasi',

                'message' => "Pendaftaran Anda untuk lomba '{$registration->contest->judul_lomba}' tidak dapat disetujui. Alasan: {$request->catatan_admin}",

                'data' => [

                    'contest_id' => $registration->id_contest,

                    'url' => route('public.contests.show', $registration->id_contest),

                ],

                'is_read' => false,

            ]);

        }



        if ($request->status_pembayaran === 'lunas' && $oldPaymentStatus !== 'lunas') {

            Notification::create([

                'id_user' => $registration->id_user,

                'type' => 'contest_payment_verified',

                'title' => 'Pembayaran Lomba Terkonfirmasi',

                'message' => "Pembayaran registrasi Anda untuk lomba '{$registration->contest->judul_lomba}' telah diverifikasi Lunas. Anda resmi terdaftar!",

                'data' => [

                    'contest_id' => $registration->id_contest,

                    'url' => route('public.contests.show', $registration->id_contest),

                ],

                'is_read' => false,

            ]);

        }



        return redirect()->back()->with('success', "Status pendaftaran peserta '{$registration->nama_lengkap}' berhasil diperbarui.");

    }



    /**

     * Setujui Calon Peserta Lomba.

     */

    public function approveParticipant($registrationId)

    {

        $registration = \App\Models\ContestRegistration::with('contest')->findOrFail($registrationId);

        $contest = $registration->contest;



        $statusBayar = ($contest->biaya_pendaftaran > 0) ? 'menunggu_pembayaran' : 'gratis';

        $deadline = ($contest->biaya_pendaftaran > 0) ? now()->addHours(24) : null;



        $registration->update([

            'status_pendaftaran' => 'disetujui',

            'status_pembayaran' => $statusBayar,

            'waktu_batas_pembayaran' => $deadline,

            'catatan_admin' => null,

        ]);



        $message = ($contest->biaya_pendaftaran > 0) 

            ? "Pendaftaran Anda untuk lomba '{$contest->judul_lomba}' telah DISETUJUI! Silakan lakukan transfer pembayaran sebelum batas waktu berakhir."

            : "Selamat! Pendaftaran Anda untuk lomba '{$contest->judul_lomba}' telah DISETUJUI dan Anda resmi terdaftar sebagai peserta.";



        Notification::create([

            'id_user' => $registration->id_user,

            'type' => 'contest_participant_approved',

            'title' => 'Pendaftaran Lomba Disetujui',

            'message' => $message,

            'data' => [

                'contest_id' => $contest->id,

                'url' => route('public.contests.show', $contest->id),

            ],

            'is_read' => false,

        ]);



        return redirect()->back()->with('success', "Calon peserta '{$registration->nama_lengkap}' berhasil disetujui!");

    }



    /**

     * Tolak Calon Peserta Lomba.

     */

    public function rejectParticipant(Request $request, $registrationId)

    {

        $registration = \App\Models\ContestRegistration::with('contest')->findOrFail($registrationId);



        $request->validate([

            'catatan_admin' => 'required|string|min:3',

        ], [

            'catatan_admin.required' => 'Tuliskan alasan penolakan calon peserta.',

        ]);



        $registration->update([

            'status_pendaftaran' => 'ditolak',

            'catatan_admin' => $request->catatan_admin,

        ]);



        Notification::create([

            'id_user' => $registration->id_user,

            'type' => 'contest_participant_rejected',

            'title' => 'Pendaftaran Lomba Tidak Lolos Kurasi',

            'message' => "Pendaftaran Anda untuk lomba '{$registration->contest->judul_lomba}' tidak dapat disetujui. Alasan: {$request->catatan_admin}",

            'data' => [

                'contest_id' => $registration->id_contest,

                'url' => route('public.contests.show', $registration->id_contest),

            ],

            'is_read' => false,

        ]);



        return redirect()->back()->with('success', "Calon peserta '{$registration->nama_lengkap}' telah ditolak dengan catatan alasan.");

    }



    /**

     * Konfirmasi / Verifikasi Bukti Pembayaran Peserta.

     */

    public function verifyPayment($registrationId)

    {

        $registration = \App\Models\ContestRegistration::with('contest')->findOrFail($registrationId);



        $registration->update([

            'status_pembayaran' => 'lunas',

        ]);



        Notification::create([

            'id_user' => $registration->id_user,

            'type' => 'contest_payment_verified',

            'title' => 'Pembayaran Lomba Terkonfirmasi',

            'message' => "Pembayaran registrasi Anda untuk lomba '{$registration->contest->judul_lomba}' telah diverifikasi Lunas. Anda resmi terdaftar!",

            'data' => [

                'contest_id' => $registration->id_contest,

                'url' => route('public.contests.show', $registration->id_contest),

            ],

            'is_read' => false,

        ]);



        return redirect()->back()->with('success', "Bukti transfer peserta '{$registration->nama_lengkap}' berhasil diverifikasi (Status: Lunas).");

    }



    /**

     * Kelola Event Lomba Foto Terpadu (Tab 1: Pendaftar, Tab 2: Submisi).

     */

    public function manage(Request $request, $id)

    {

        $contest = PhotoContest::with(['creator', 'admin'])->withCount(['submissions', 'registrations'])->findOrFail($id);



        $tab = $request->query('tab', 'pendaftar');

        if (!in_array($tab, ['pendaftar', 'submisi'])) $tab = 'pendaftar';



        $search = $request->query('search', '');

        $status = $request->query('status', 'all');

        $sort = $request->query('sort', 'latest');

        $perPage = (int) $request->query('per_page', 10);

        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;



        // 1. Data Pendaftar (Registrations)

        $regQuery = \App\Models\ContestRegistration::with('user')

            ->where('id_contest', $contest->id);



        if ($tab === 'pendaftar' && !empty($search)) {

            $regQuery->where(function ($q) use ($search) {

                $q->where('nama_lengkap', 'like', "%{$search}%")

                  ->orWhere('email', 'like', "%{$search}%")

                  ->orWhere('no_wa', 'like', "%{$search}%")

                  ->orWhere('instansi', 'like', "%{$search}%")

                  ->orWhere('asal_kota', 'like', "%{$search}%");

            });

        }



        if ($tab === 'pendaftar' && $status && $status !== 'all') {

            if ($status === 'pending' || $status === 'menunggu_verifikasi') {

                $regQuery->whereIn('status_pendaftaran', ['pending', 'menunggu_verifikasi']);

            } else {

                $regQuery->where('status_pendaftaran', $status);

            }

        }



        if ($tab === 'pendaftar' && $sort === 'oldest') {

            $regQuery->orderBy('created_at', 'asc');

        } else {

            $regQuery->orderBy('created_at', 'desc');

        }



        $participants = $regQuery->paginate($perPage, ['*'], 'p_page')->withQueryString();



        // 2. Data Submisi (Karya Foto)

        $subQuery = ContestSubmission::with('user')

            ->where('id_contest', $contest->id);



        if ($tab === 'submisi' && !empty($search)) {

            $subQuery->where(function ($q) use ($search) {

                $q->where('judul_karya', 'like', "%{$search}%")

                  ->orWhere('deskripsi_karya', 'like', "%{$search}%")

                  ->orWhereHas('user', function ($uq) use ($search) {

                      $uq->where('nama', 'like', "%{$search}%")

                         ->orWhere('email', 'like', "%{$search}%");

                  });

            });

        }



        if ($tab === 'submisi' && $status && $status !== 'all') {

            if ($status === 'winners') {

                $subQuery->whereIn('status_submission', ['winner_1', 'winner_2', 'winner_3']);

            } elseif ($status === 'rejected_disqualified') {

                $subQuery->whereIn('status_submission', ['rejected', 'disqualified']);

            } else {

                $subQuery->where('status_submission', $status);

            }

        }



        if ($tab === 'submisi' && $sort === 'oldest') {

            $subQuery->orderBy('created_at', 'asc');

        } elseif ($tab === 'submisi' && $sort === 'winners_first') {

            $subQuery->orderByRaw("FIELD(status_submission, 'winner_1', 'winner_2', 'winner_3', 'verified', 'rejected', 'disqualified') ASC, created_at DESC");

        } else {

            $subQuery->orderBy('created_at', 'desc');

        }



        $submissions = $subQuery->paginate($perPage, ['*'], 's_page')->withQueryString();



        // Metrics Registrations

        $totalParticipants = \App\Models\ContestRegistration::where('id_contest', $contest->id)->count();

        $pendingParticipants = \App\Models\ContestRegistration::where('id_contest', $contest->id)->whereIn('status_pendaftaran', ['pending', 'menunggu_verifikasi'])->count();

        $approvedParticipants = \App\Models\ContestRegistration::where('id_contest', $contest->id)->where('status_pendaftaran', 'disetujui')->count();

        $rejectedParticipants = \App\Models\ContestRegistration::where('id_contest', $contest->id)->where('status_pendaftaran', 'ditolak')->count();



        // Metrics Submissions

        $totalSubmissions = ContestSubmission::where('id_contest', $contest->id)->count();

        $totalWinners = ContestSubmission::where('id_contest', $contest->id)->whereIn('status_submission', ['winner_1', 'winner_2', 'winner_3'])->count();

        $totalVerified = ContestSubmission::where('id_contest', $contest->id)->where('status_submission', 'verified')->count();

        $totalRejected = ContestSubmission::where('id_contest', $contest->id)->whereIn('status_submission', ['rejected', 'disqualified'])->count();



        return view('admin.contests.manage', compact(

            'contest',

            'tab',

            'search',

            'status',

            'sort',

            'participants',

            'submissions',

            'totalParticipants',

            'pendingParticipants',

            'approvedParticipants',

            'rejectedParticipants',

            'totalSubmissions',

            'totalWinners',

            'totalVerified',

            'totalRejected'

        ));

    }



    /**

     * Redirect to Manage (Tab Pendaftar)

     */

    public function participants(Request $request, $id)

    {

        return redirect()->route('admin.contests.manage', ['id' => $id, 'tab' => 'pendaftar']);

    }



    /**

     * Redirect to Manage (Tab Submisi)

     */

    public function submissions(Request $request, $id)

    {

        return redirect()->route('admin.contests.manage', ['id' => $id, 'tab' => 'submisi']);

    }



    /**

     * Penetapan Pemenang Juara Lomba Foto (1, 2, 3, verified, rejected, disqualified).

     */

    public function setWinner(Request $request, $submissionId)

    {

        $submission = ContestSubmission::findOrFail($submissionId);



        $request->validate([

            'status_submission' => 'required|in:verified,winner_1,winner_2,winner_3,rejected,disqualified',

            'catatan_juri' => 'nullable|string|max:1000',

        ]);



        if (in_array($request->status_submission, ['winner_1', 'winner_2', 'winner_3'])) {

            // Reset existing winner for this rank in the same contest to avoid duplicates

            ContestSubmission::where('id_contest', $submission->id_contest)

                ->where('id', '!=', $submission->id)

                ->where('status_submission', $request->status_submission)

                ->update(['status_submission' => 'verified']);

        }



        $submission->update([

            'status_submission' => $request->status_submission,

            'catatan_juri' => $request->catatan_juri,

        ]);



        return redirect()->back()->with('success', "Penilaian karya '{$submission->judul_karya}' berhasil disimpan.");

    }

}