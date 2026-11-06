<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumPostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $query = ForumPost::with(['user', 'comments.user', 'medias']);

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

        if (!empty($category) && $category !== 'semua') {
            $query->where('kategori', $category);
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $posts = $query->paginate($perPage)->withQueryString();
        $totalPosts = ForumPost::count();

        // Popular Categories List for Sidebar (Real Accurate Counts)
        $categoriesList = [
            ['name' => 'Teknis & Pengaturan Lensa', 'slug' => 'teknis', 'count' => ForumPost::where('kategori', 'teknis')->count()],
            ['name' => 'Rekomendasi Spot Foto Outdoor', 'slug' => 'spot_foto', 'count' => ForumPost::where('kategori', 'spot_foto')->count()],
            ['name' => 'Peralatan & Aksesoris Studio', 'slug' => 'peralatan', 'count' => ForumPost::where('kategori', 'peralatan')->count()],
            ['name' => 'Lainnya & Diskusi Umum', 'slug' => 'lainnya', 'count' => ForumPost::where('kategori', 'lainnya')->count()],
        ];

        return view('public.forum.index', compact('posts', 'search', 'category', 'sort', 'totalPosts', 'categoriesList'));
    }

    /**
     * Tampilkan Detail Postingan & Diskusi Komentar.
     */
    public function show(Request $request, $id)
    {
        $commentSort = $request->query('comment_sort', 'newest');

        $post = ForumPost::withCount('comments')
            ->with([
                'user', 
                'medias',
                'comments' => function ($q) use ($commentSort) {
                    $q->whereNull('parent_id');
                    if ($commentSort === 'popular') {
                        $q->orderBy('likes', 'desc')->orderBy('created_at', 'desc');
                    } else {
                        $q->orderBy('created_at', 'desc');
                    }
                    $q->with(['user', 'replies.user']);
                }
            ])->findOrFail($id);

        $post->increment('views');
        
        $relatedPosts = ForumPost::with('medias')->where('id', '!=', $post->id)->latest()->take(5)->get();

        $categoriesList = [
            ['name' => 'Teknis & Pengaturan Lensa', 'slug' => 'teknis', 'count' => ForumPost::where('kategori', 'teknis')->count()],
            ['name' => 'Rekomendasi Spot Foto Outdoor', 'slug' => 'spot_foto', 'count' => ForumPost::where('kategori', 'spot_foto')->count()],
            ['name' => 'Peralatan & Aksesoris Studio', 'slug' => 'peralatan', 'count' => ForumPost::where('kategori', 'peralatan')->count()],
            ['name' => 'Lainnya & Diskusi Umum', 'slug' => 'lainnya', 'count' => ForumPost::where('kategori', 'lainnya')->count()],
        ];

        return view('public.forum.show', compact('post', 'relatedPosts', 'categoriesList', 'commentSort'));
    }

    /**
     * Buat Topik Diskusi Baru di Forum.
     */
    public function storePost(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login terlebih dahulu untuk berdiskusi.');
        }

        $request->validate([
            'judul' => 'required|string|min:5|max:255',
            'kategori' => 'nullable|string|in:teknis,spot_foto,peralatan,lainnya',
            'deskripsi' => 'required|string|min:10',
            'foto_files' => 'nullable|array|max:5',
            'foto_files.*' => 'image|mimes:jpeg,jpg,png,webp|max:10240',
        ], [
            'judul.required' => 'Judul diskusi harus diisi.',
            'deskripsi.required' => 'Isi topik diskusi harus dituliskan.',
            'foto_files.max' => 'Maksimal 5 foto yang dapat diunggah.',
            'foto_files.*.image' => 'File harus berupa gambar.',
            'foto_files.*.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto_files.*.max' => 'Ukuran setiap foto maksimal 10MB.',
        ]);

        $post = ForumPost::create([
            'id_user' => $user->id,
            'judul' => trim($request->judul),
            'kategori' => $request->input('kategori', 'lainnya'),
            'deskripsi' => trim($request->deskripsi),
        ]);

        if ($request->hasFile('foto_files')) {
            $files = array_slice($request->file('foto_files'), 0, 5);
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('forum/posts', 'public');
                    ForumPostMedia::create([
                        'id_forum_post' => $post->id,
                        'foto' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('public.forum.show', $post->id)->with('success', 'Topik diskusi baru berhasil diterbitkan di forum!');
    }

    /**
     * Tambah Balasan Komentar pada Postingan Forum.
     */
    public function storeComment(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login untuk membalas diskusi.');
        }

        $post = ForumPost::findOrFail($id);

        $request->validate([
            'comment' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:forum_comments,id',
        ], [
            'comment.required' => 'Tuliskan balasan komentar Anda.',
        ]);

        $parentId = null;
        if ($request->filled('parent_id')) {
            $parentComment = ForumComment::where('id', $request->parent_id)
                ->where('id_forum_post', $post->id)
                ->first();
            if ($parentComment) {
                // If the target comment is already a reply, link to root comment to maintain 1-level clean indentation
                $parentId = $parentComment->parent_id ?? $parentComment->id;
            }
        }

        ForumComment::create([
            'id_forum_post' => $post->id,
            'id_user' => $user->id,
            'parent_id' => $parentId,
            'comment' => trim($request->comment),
        ]);

        return redirect()->back()->with('success', $parentId ? 'Balasan komentar berhasil dikirimkan.' : 'Balasan komentar berhasil dikirimkan.');
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

    /**
     * Laporkan Komentar yang Melanggar Pedoman Komunitas.
     */
    public function reportComment(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login untuk melaporkan komentar.');
        }

        $comment = ForumComment::findOrFail($id);

        if ($comment->id_user === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat melaporkan komentar Anda sendiri.');
        }

        $alreadyReported = \App\Models\ForumCommentReport::where('id_comment', $comment->id)
            ->where('id_user', $user->id)
            ->exists();

        if ($alreadyReported) {
            return redirect()->back()->with('error', 'Anda sudah pernah melaporkan komentar ini sebelumnya.');
        }

        $request->validate([
            'kategori' => 'required|string|in:spam,harassment,inappropriate,misinformation,other',
            'alasan' => 'nullable|string|max:500',
        ], [
            'kategori.required' => 'Pilih kategori pelanggaran.',
            'kategori.in' => 'Kategori pelanggaran tidak valid.',
        ]);

        \App\Models\ForumCommentReport::create([
            'id_comment' => $comment->id,
            'id_user' => $user->id,
            'kategori' => $request->kategori,
            'alasan' => trim($request->alasan),
            'status' => 'pending',
        ]);

        // Auto-flagging / Soft-hide jika mencapai threshold >= 3 laporan unik
        $totalReports = \App\Models\ForumCommentReport::where('id_comment', $comment->id)->count();
        if ($totalReports >= 3 && !$comment->is_hidden) {
            $comment->update(['is_hidden' => true]);

            // Kirim notifikasi peringatan ke seluruh admin
            $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $adm) {
                \App\Models\Notification::create([
                    'id_user' => $adm->id,
                    'type' => 'forum_report',
                    'title' => 'Komentar Forum Disembunyikan',
                    'message' => "Komentar telah menerima {$totalReports} laporan dan disembunyikan otomatis. Menunggu peninjauan admin.",
                    'data' => [
                        'comment_id' => $comment->id,
                        'url' => route('admin.forum', ['tab' => 'reports'])
                    ],
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Laporan berhasil dikirim dan akan ditinjau oleh tim admin.');
    }

    /**
     * Hapus Komentar oleh Pembuat Komentar Sendiri.
     */
    public function deleteComment(Request $request, $id)
    {
        $user = Auth::user();
        $comment = ForumComment::findOrFail($id);

        if ($comment->id_user !== $user->id && !in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $comment->delete();
        return redirect()->back()->with('success', 'Komentar Anda berhasil dihapus.');
    }
}