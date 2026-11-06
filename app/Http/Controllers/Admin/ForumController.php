<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /**
     * Tampilkan Dashboard Moderasi Forum Admin.
     */
        public function index(Request $request)
    {
        $tab = $request->query('tab', 'discussions');
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'latest');

        $query = ForumPost::with(['user', 'comments.user'])->withCount('comments');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'most_comments') {
            $query->orderByDesc('comments_count');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $posts = $query->paginate($perPage)->withQueryString();
        $totalPosts = ForumPost::count();
        $totalComments = ForumComment::count();
        $thisMonthPosts = ForumPost::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $topPost = ForumPost::withCount('comments')->orderByDesc('comments_count')->first();

        // Reported Comments Data
        $reportedQuery = ForumComment::has('reports')
            ->with(['user', 'post', 'reports.user'])
            ->withCount(['reports as pending_reports_count' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->withCount('reports');

        if (!empty($search) && $tab === 'reports') {
            $reportedQuery->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $reportedComments = $reportedQuery->orderByDesc('pending_reports_count')->orderByDesc('created_at')->paginate($perPage, ['*'], 'reports_page')->withQueryString();
        $pendingReportsCount = \App\Models\ForumCommentReport::where('status', 'pending')->count();

        return view('admin.forum.index', compact(
            'posts', 
            'search', 
            'sort', 
            'totalPosts', 
            'totalComments', 
            'thisMonthPosts', 
            'topPost',
            'tab',
            'reportedComments',
            'pendingReportsCount'
        ));
    }

    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);
        $post->comments()->delete();
        $post->delete();

        return redirect()->back()->with('success', 'Postingan forum berhasil dihapus dari komunitas.');
    }

    /**
     * Moderasi: Hapus Komentar Forum Spam / Pelanggaran.
     */
    public function destroyComment($id)
    {
        $comment = ForumComment::findOrFail($id);
        \App\Models\ForumCommentReport::where('id_comment', $comment->id)->update(['status' => 'reviewed']);
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar forum berhasil dihapus.');
    }

    /**
     * Moderasi: Abaikan Laporan Komentar & Pulihkan Tampilan.
     */
    public function dismissReports($id)
    {
        $comment = ForumComment::findOrFail($id);
        $comment->update(['is_hidden' => false]);
        \App\Models\ForumCommentReport::where('id_comment', $comment->id)->update(['status' => 'dismissed']);

        return redirect()->back()->with('success', 'Laporan berhasil diabaikan dan komentar dipulihkan.');
    }
}