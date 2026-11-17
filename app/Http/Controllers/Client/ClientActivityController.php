<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ELearningProgress;
use App\Models\ElearningTransaction;
use App\Models\ContestRegistration;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientActivityController extends Controller
{
    /**
     * Tampilkan halaman terpadu Aktivitas Komunitas Klien (E-Learning, Lomba Foto, dan Forum).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Pastikan transaksi e-learning sukses tersinkronisasi ke tabel progress
        $paidTransactions = ElearningTransaction::where('id_user', $userId)
            ->where('payment_status', 'sukses')
            ->pluck('id_e_learning');

        foreach ($paidTransactions as $courseId) {
            ELearningProgress::firstOrCreate(
                ['id_user' => $userId, 'id_e_learning' => $courseId],
                ['status' => 'sedang_belajar', 'progress_percentage' => 0]
            );
        }

        // 1. Data E-Learning
        $elearningProgresses = ELearningProgress::with(['elearning.lessons', 'elearning.lessonProgresses' => function ($q) use ($userId) {
            $q->where('id_user', $userId);
        }])
            ->where('id_user', $userId)
            ->latest('updated_at')
            ->get();

        // 2. Data Lomba Foto
        $contestRegistrations = ContestRegistration::with(['contest', 'submissions'])
            ->where('id_user', $userId)
            ->latest()
            ->get();

        // 3. Data Forum Komunitas
        $forumPosts = ForumPost::withCount('comments')
            ->where('id_user', $userId)
            ->latest()
            ->get();

        // Metrik Ringkasan (Rule #64 Compliant: 2 Baris Murni text-xs)
        $stats = [
            'elearning_active' => $elearningProgresses->where('status', '!=', 'selesai')->count(),
            'elearning_completed' => $elearningProgresses->where('status', 'selesai')->count(),
            'contests_total' => $contestRegistrations->count(),
            'forum_posts_total' => $forumPosts->count(),
        ];

        // Validasi parameter tab aktif
        $activeTab = $request->query('tab', 'elearning');
        if (!in_array($activeTab, ['elearning', 'contests', 'forum'])) {
            $activeTab = 'elearning';
        }

        return view('client.activities.index', compact(
            'elearningProgresses',
            'contestRegistrations',
            'forumPosts',
            'stats',
            'activeTab'
        ));
    }
}
