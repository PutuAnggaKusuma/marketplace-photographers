<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Elearning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElearningController extends Controller
{
    /**
     * Tampilkan Overview Landing Akademi E-Learning.
     */
    public function index(Request $request)
    {
        // Overview preview: Take top 6 MOST READ courses (sorted by view_count DESC)
        $elearnings = Elearning::orderBy('view_count', 'desc')->take(6)->get();
        $categories = Elearning::distinct()->pluck('kategori');

        // Dynamic Aggregate Stats
        $totalCategories = Elearning::distinct('kategori')->count();
        $totalViews = Elearning::sum('view_count');
        $totalCourses = Elearning::count();
        $heroCourses = Elearning::take(3)->get();

        return view('public.elearning.index', compact(
            'elearnings', 
            'categories', 
            'totalCategories',
            'totalViews',
            'totalCourses',
            'heroCourses'
        ));
    }

    /**
     * Tampilkan Halaman Full Data Katalog E-Learning (Search, Filter & Pagination).
     */
        public function katalog(Request $request)
    {
        $selectedCategory = $request->query('category');
        $sort = $request->query('sort', 'latest');
        $search = $request->query('q');

        $query = Elearning::query();

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('kategori', $selectedCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('ringkasan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'most_views') {
            $query->orderBy('view_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $elearnings = $query->paginate($perPage)->withQueryString();
        $categories = Elearning::distinct()->pluck('kategori');

        return view('public.elearning.katalog', compact(
            'elearnings', 
            'categories', 
            'selectedCategory', 
            'sort',
            'search'
        ));
    }

    public function show($slug, Request $request)
    {
        $course = Elearning::with(['modules.lessons', 'lessons'])->where('slug', $slug)->firstOrFail();
        $course->increment('view_count');

        $userId = Auth::id();
        $allLessons = $course->lessons()->orderBy('urutan', 'asc')->get();

        // Cari tahapan aktif berdasarkan request atau tahapan pertama
        $lessonId = $request->query('lesson');
        $currentLesson = null;

        if ($lessonId) {
            $currentLesson = $allLessons->firstWhere('id', (int) $lessonId);
        }

        if (!$currentLesson) {
            $currentLesson = $allLessons->first();
        }

        // List ID tahapan yang sudah diselesaikan oleh user
        $completedLessonIds = [];
        if ($userId) {
            $completedLessonIds = \App\Models\ElearningLessonProgress::where('id_user', $userId)
                ->where('id_elearning', $course->id)
                ->where('is_completed', true)
                ->pluck('id_lesson')
                ->toArray();
        }

        // Hitung persentase progress
        $totalLessons = $allLessons->count();
        $userProgressPercentage = $totalLessons > 0 
            ? min(100, (int) round((count($completedLessonIds) / $totalLessons) * 100))
            : 0;

        // Cari prev & next lesson
        $prevLesson = null;
        $nextLesson = null;
        if ($currentLesson && $allLessons->isNotEmpty()) {
            $currentIndex = $allLessons->search(fn($l) => $l->id === $currentLesson->id);
            if ($currentIndex !== false) {
                if ($currentIndex > 0) {
                    $prevLesson = $allLessons->get($currentIndex - 1);
                }
                if ($currentIndex < $allLessons->count() - 1) {
                    $nextLesson = $allLessons->get($currentIndex + 1);
                }
            }
        }

        $userProgress = $userId ? $course->getUserProgress() : null;

        // Cek status kepemilikan / pembelian kursus dari Database MySQL
        $isPurchased = false;
        if ($course->harga == 0 || !$course->is_premium) {
            $isPurchased = true;
        } elseif ($userId) {
            $isPurchased = \App\Models\ElearningTransaction::where('id_user', $userId)
                ->where('id_e_learning', $course->id)
                ->where('payment_status', 'completed')
                ->exists();
            if (!$isPurchased) {
                $isPurchased = session()->get("elearning_purchased_{$course->id}", false);
            }
        }

        // Tentukan apakah tahapan saat ini terkunci (harus bayar)
        $isCurrentLessonLocked = false;
        if ($currentLesson && !$currentLesson->is_free && !$isPurchased) {
            $isCurrentLessonLocked = true;
        }

        // Cari tahapan gratis pertama jika ada
        $firstFreeLesson = $allLessons->firstWhere('is_free', true);

        return response()
            ->view('public.elearning.show', compact(
                'course',
                'allLessons',
                'currentLesson',
                'completedLessonIds',
                'userProgressPercentage',
                'prevLesson',
                'nextLesson',
                'userProgress',
                'isPurchased',
                'isCurrentLessonLocked',
                'firstFreeLesson'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Toggle status penyelesaian tahapan materi spesifik (Coursera Step Completion).
     */
    public function toggleLessonProgress(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu untuk mencatat progress belajar Anda.',
            ], 401);
        }

        $lesson = \App\Models\ElearningLesson::with('elearning')->findOrFail($id);
        $course = $lesson->elearning;
        $userId = Auth::id();

        $lessonProgress = \App\Models\ElearningLessonProgress::firstOrNew([
            'id_user' => $userId,
            'id_elearning' => $course->id,
            'id_lesson' => $lesson->id,
        ]);

        $lessonProgress->is_completed = true;
        if (!$lessonProgress->completed_at) {
            $lessonProgress->completed_at = now();
        }
        $lessonProgress->save();
        $isCompleted = true;
        $message = 'Materi berhasil diselesaikan!';

        // Sinkronisasi ke tabel induk e_learning_progress
        $totalLessons = $course->lessons()->count();
        $completedLessonsCount = \App\Models\ElearningLessonProgress::where('id_user', $userId)
            ->where('id_elearning', $course->id)
            ->where('is_completed', true)
            ->count();

        $percentage = $totalLessons > 0 ? min(100, (int) round(($completedLessonsCount / $totalLessons) * 100)) : 100;

        $overallProgress = \App\Models\ELearningProgress::firstOrNew([
            'id_user' => $userId,
            'id_e_learning' => $course->id,
        ]);

        $overallProgress->progress_percentage = $percentage;
        if ($percentage >= 100) {
            $overallProgress->status = 'selesai';
            $overallProgress->completed_at = now();

            // Kirim notifikasi jika baru saja menuntaskan 100% kursus
            \App\Models\Notification::create([
                'id_user' => $userId,
                'type' => 'elearning',
                'title' => 'Selamat! Kursus Selesai',
                'message' => 'Anda telah menyelesaikan seluruh modul pada kursus: ' . $course->judul,
                'data' => [
                    'slug' => $course->slug,
                    'url' => route('public.elearning.show', $course->slug),
                ],
                'is_read' => false,
            ]);
        } else {
            $overallProgress->status = $percentage > 0 ? 'sedang_belajar' : 'belum_mulai';
            $overallProgress->completed_at = null;
        }
        $overallProgress->save();

        return response()->json([
            'success' => true,
            'is_completed' => $isCompleted,
            'percentage' => $percentage,
            'completed_lessons_count' => $completedLessonsCount,
            'total_lessons' => $totalLessons,
            'message' => $message,
        ]);
    }

    /**
     * Tandai materi telah selesai dipelajari (Mark as Completed) atau toggle kembali.
     */
    public function toggleProgress(Request $request, $id)
    {
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu untuk menyimpan progres belajar Anda.',
                ], 401);
            }
            return redirect()->guest(route('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $course = Elearning::findOrFail($id);

        $progress = \App\Models\ELearningProgress::firstOrNew([
            'id_user' => Auth::id(),
            'id_e_learning' => $course->id,
        ]);

        if ($progress->exists && $progress->status === 'selesai') {
            // Toggle kembali ke belum selesai
            $progress->status = 'sedang_belajar';
            $progress->progress_percentage = 50;
            $progress->completed_at = null;
            $progress->save();

            $isCompleted = false;
            $message = 'Status materi diubah menjadi sedang dipelajari.';
        } else {
            // Tandai selesai
            $progress->status = 'selesai';
            $progress->progress_percentage = 100;
            $progress->completed_at = now();
            $progress->save();

            $isCompleted = true;
            $message = 'Selamat! Materi berhasil ditandai telah selesai dipelajari.';

            // In-app Notification ke User (Rule #26)
            \App\Models\Notification::create([
                'id_user' => Auth::id(),
                'type' => 'elearning',
                'title' => 'Modul E-Learning Selesai!',
                'message' => 'Selamat, Anda telah menyelesaikan modul: ' . $course->judul,
                'data' => [
                    'slug' => $course->slug,
                    'url' => route('public.elearning.show', $course->slug),
                ],
                'is_read' => false,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_completed' => $isCompleted,
                'status' => $progress->status,
                'completed_at' => $progress->completed_at ? $progress->completed_at->format('d M Y, H:i') : null,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Halaman Pembayaran / Checkout Akses Kursus E-Learning.
     */
    public function checkout($slug)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('warning', 'Silakan masuk ke akun Anda terlebih dahulu untuk melanjutkan proses checkout kursus.');
        }

        $course = Elearning::where('slug', $slug)
            ->with(['modules.lessons'])
            ->firstOrFail();

        // Jika gratis, langsung berikan akses
        if ($course->harga == 0 || !$course->is_premium) {
            \App\Models\ELearningProgress::firstOrCreate(
                ['id_user' => Auth::id(), 'id_e_learning' => $course->id],
                ['status' => 'sedang_belajar', 'progress_percentage' => 0]
            );
            return redirect()->route('public.elearning.show', $course->slug)->with('info', 'Kursus ini gratis dan langsung terbuka untuk Anda.');
        }

        // Cek apakah sudah pernah dibeli
        $alreadyPurchased = \App\Models\ElearningTransaction::where('id_user', Auth::id())
            ->where('id_e_learning', $course->id)
            ->where('payment_status', 'completed')
            ->exists();

        if ($alreadyPurchased) {
            return redirect()->route('public.elearning.show', $course->slug)->with('info', 'Anda sudah memiliki akses penuh ke kursus ini.');
        }

        return view('public.elearning.checkout', compact('course'));
    }

    /**
     * Proses Konfirmasi Pembayaran Kursus E-Learning (Tercatat di Database).
     */
    public function processCheckout(Request $request, $slug)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('warning', 'Silakan masuk ke akun Anda terlebih dahulu untuk menyelesaikan pembayaran.');
        }

        $course = Elearning::where('slug', $slug)->firstOrFail();

        $request->validate([
            'payment_method' => 'nullable|in:qris,bank,ewallet',
        ]);

        $method = $request->input('payment_method', 'qris');
        $invoiceCode = 'INV-ELN-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

        // Cek apakah transaksi sudah ada
        $existingTx = \App\Models\ElearningTransaction::where('id_user', Auth::id())
            ->where('id_e_learning', $course->id)
            ->where('payment_status', 'completed')
            ->first();

        if (!$existingTx) {
            $existingTx = \App\Models\ElearningTransaction::create([
                'id_user' => Auth::id(),
                'id_e_learning' => $course->id,
                'invoice_code' => $invoiceCode,
                'nominal' => $course->harga ?: 0,
                'payment_method' => $method,
                'payment_status' => 'completed',
                'paid_at' => now(),
            ]);

            // Aktifkan progress pembelajaran
            \App\Models\ELearningProgress::firstOrCreate(
                ['id_user' => Auth::id(), 'id_e_learning' => $course->id],
                ['status' => 'sedang_belajar', 'progress_percentage' => 0]
            );

            $formattedHarga = 'Rp ' . number_format($course->harga ?: 0, 0, ',', '.');

            // Kirim Notifikasi in-app
            \App\Models\Notification::send(
                Auth::id(),
                'Pembelian Kursus Berhasil',
                "Pembayaran untuk kursus '{$course->judul}' sebesar {$formattedHarga} berhasil dikonfirmasi. Akses kurikulum terbuka penuh.",
                'course_enrolled',
                route('public.elearning.show', $course->slug)
            );

            // Rekam ke Activity Log
            \App\Models\ActivityLog::record(
                'elearning_purchase',
                "Pengguna " . Auth::user()->nama . " membeli kursus '{$course->judul}' ({$invoiceCode}) seharga {$formattedHarga}",
                $course,
                ['nominal' => $course->harga, 'invoice_code' => $invoiceCode, 'method' => $method]
            );
        }

        session()->put("elearning_purchased_{$course->id}", true);

        return redirect()->route('public.elearning.show', $course->slug)->with('success', 'Pembayaran berhasil dikonfirmasi! Seluruh materi kursus kini terbuka.');
    }

    /**
     * Unduh & Tampilkan Sertifikat Kelulusan E-Learning Digital (Certificate of Completion).
     */
    public function certificate($slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $course = Elearning::with(['modules.lessons', 'lessons'])->where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // Cek apakah progress user sudah 100%
        $totalLessons = $course->lessons()->count();
        $completedLessonsCount = \App\Models\ElearningLessonProgress::where('id_user', $user->id)
            ->where('id_elearning', $course->id)
            ->where('is_completed', true)
            ->count();

        $percentage = $totalLessons > 0 ? min(100, (int) round(($completedLessonsCount / $totalLessons) * 100)) : 100;

        if ($percentage < 100 && $totalLessons > 0) {
            return redirect()->route('public.elearning.show', $course->slug)
                ->with('error', 'Selesaikan seluruh materi kurikulum pembelajaran terlebih dahulu untuk menerbitkan sertifikat kelulusan.');
        }

        $overallProgress = \App\Models\ELearningProgress::where('id_user', $user->id)
            ->where('id_e_learning', $course->id)
            ->first();

        $completedAt = $overallProgress?->completed_at ? \Carbon\Carbon::parse($overallProgress->completed_at) : now();

        // Generate nomor seri kredensial sertifikat unik
        $credentialId = 'LM-CERT-' . strtoupper(substr(md5($user->id . '-' . $course->id . '-lensmatch-academy-cert'), 0, 8)) . '-' . $completedAt->format('Y');

        return view('public.elearning.certificate', compact(
            'course',
            'user',
            'completedAt',
            'credentialId',
            'percentage',
            'totalLessons'
        ));
    }

    /**
     * Tampilkan Dokumen Invoice Resmi Transaksi E-Learning Print/PDF Ready.
     */
    public function printInvoice(Request $request, $id)
    {
        $transaction = \App\Models\ElearningTransaction::with(['elearning', 'user'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        return view('public.elearning.invoice-print', compact('transaction'));
    }
}