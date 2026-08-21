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
        $query = ForumPost::with(['user', 'comments.user'])->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
        }

        $posts = $query->paginate(10)->withQueryString();
        $totalPosts = ForumPost::count();
        $totalComments = ForumComment::count();

        return view('admin.forum.index', compact('posts', 'search', 'totalPosts', 'totalComments'));
    }

    /**
     * Moderasi: Hapus Postingan Forum Spam.
     */
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