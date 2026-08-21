<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Tampilkan Halaman Utama Komunitas Forum Diskusi (Mirip AnalisAnggaran UI).
     */
    public function index(Request $request)
    {
        $search = $request->query('q', '');
        $category = $request->query('cat', 'semua');
        $sort = $request->query('sort', 'terbaru');

        $query = ForumPost::with(['user', 'comments.user']);

        if ($sort === 'terpopuler') {
            $query->withCount('comments')->orderBy('comments_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

                $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $posts = $query->paginate($perPage)->withQueryString();
        $totalPosts = ForumPost::count();

        // Popular Categories List for Sidebar
        $totalCount = ForumPost::count();
        $categoriesList = [
            ['name' => 'Teknis & Pengaturan Lensa', 'slug' => 'teknis', 'count' => ForumPost::where('judul', 'like', '%lensa%')->orWhere('deskripsi', 'like', '%lensa%')->count()],
            ['name' => 'Rekomendasi Spot Foto Outdoor', 'slug' => 'spot_foto', 'count' => ForumPost::where('judul', 'like', '%spot%')->orWhere('deskripsi', 'like', '%spot%')->count()],
            ['name' => 'Peralatan & Aksesoris Studio', 'slug' => 'peralatan', 'count' => ForumPost::where('judul', 'like', '%peralatan%')->orWhere('deskripsi', 'like', '%peralatan%')->count()],
            ['name' => 'Lainnya & Diskusi Umum', 'slug' => 'lainnya', 'count' => $totalCount],
        ];

        return view('public.forum.index', compact('posts', 'search', 'category', 'sort', 'totalPosts', 'categoriesList'));
    }

    /**
     * Tampilkan Detail Postingan & Diskusi Komentar.
     */
    public function show($id)
    {
        $post = ForumPost::with(['user', 'comments.user'])->findOrFail($id);
        $post->increment('views');
        
        $relatedPosts = ForumPost::where('id', '!=', $post->id)->latest()->take(5)->get();

        $totalCount = ForumPost::count();
        $categoriesList = [
            ['name' => 'Teknis & Pengaturan Lensa', 'slug' => 'teknis', 'count' => ForumPost::where('judul', 'like', '%lensa%')->orWhere('deskripsi', 'like', '%lensa%')->count()],
            ['name' => 'Rekomendasi Spot Foto Outdoor', 'slug' => 'spot_foto', 'count' => ForumPost::where('judul', 'like', '%spot%')->orWhere('deskripsi', 'like', '%spot%')->count()],
            ['name' => 'Peralatan & Aksesoris Studio', 'slug' => 'peralatan', 'count' => ForumPost::where('judul', 'like', '%peralatan%')->orWhere('deskripsi', 'like', '%peralatan%')->count()],
            ['name' => 'Lainnya & Diskusi Umum', 'slug' => 'lainnya', 'count' => $totalCount],
        ];

        return view('public.forum.show', compact('post', 'relatedPosts', 'categoriesList'));
    }

    /**
     * Buat Topik Diskusi Baru di Forum.
     */
    public function storePost(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk berdiskusi.');
        }

        $request->validate([
            'judul' => 'required|string|min:5|max:255',
            'deskripsi' => 'required|string|min:10',
        ], [
            'judul.required' => 'Judul diskusi harus diisi.',
            'deskripsi.required' => 'Isi topik diskusi harus dituliskan.',
        ]);

        $post = ForumPost::create([
            'id_user' => $user->id,
            'judul' => trim($request->judul),
            'deskripsi' => trim($request->deskripsi),
        ]);

        return redirect()->route('public.forum.show', $post->id)->with('success', 'Topik diskusi baru berhasil diterbitkan di forum!');
    }

    /**
     * Tambah Balasan Komentar pada Postingan Forum.
     */
    public function storeComment(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk membalas diskusi.');
        }

        $post = ForumPost::findOrFail($id);

        $request->validate([
            'comment' => 'required|string|min:3|max:1000',
        ], [
            'comment.required' => 'Tuliskan balasan komentar Anda.',
        ]);

        ForumComment::create([
            'id_forum_post' => $post->id,
            'id_user' => $user->id,
            'comment' => trim($request->comment),
        ]);

        return redirect()->back()->with('success', 'Balasan komentar berhasil dikirimkan.');
    }

    public function likeComment($id)
    {
        $comment = ForumComment::findOrFail($id);
        $comment->increment('likes');
        return response()->json([
            'success' => true,
            'likes' => $comment->likes,
            'dislikes' => $comment->dislikes
        ]);
    }

    public function dislikeComment($id)
    {
        $comment = ForumComment::findOrFail($id);
        $comment->increment('dislikes');
        return response()->json([
            'success' => true,
            'likes' => $comment->likes,
            'dislikes' => $comment->dislikes
        ]);
    }
}