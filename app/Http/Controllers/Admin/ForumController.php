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

        return view('admin.forum.index', compact('posts', 'search', 'sort', 'totalPosts', 'totalComments', 'thisMonthPosts', 'topPost'));
    }

    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);
        $post->comments()->delete();
        $post->delete();

        return redirect()->back()->with('success', 'Postingan forum berhasil dihapus dari komunitas.');
    }

    /**
     * Moderasi: Hapus Komentar Forum Spam.
     */
    public function destroyComment($id)
    {
        $comment = ForumComment::findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar forum berhasil dihapus.');
    }
}