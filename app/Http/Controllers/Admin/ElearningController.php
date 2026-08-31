<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Elearning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ElearningController extends Controller
{
    /**
     * Tampilkan Tabel Pengelolaan Materi E-Learning Admin.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'latest');

        $query = Elearning::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('ringkasan', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'title_asc') {
            $query->orderBy('judul', 'asc');
        } elseif ($sort === 'title_desc') {
            $query->orderBy('judul', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        
        $materials = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('nama_kategori', 'asc')->get();

        return view('admin.elearning.index', compact('materials', 'categories', 'search', 'sort'));
    }

    /**
     * Tampilkan Halaman Form Tambah Materi E-Learning Baru.
     */
    public function create()
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('admin.elearning.create', compact('categories'));
    }

    /**
     * Simpan Materi E-Learning Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'id_kategori' => 'nullable|exists:categories,id',
            'video_url' => 'nullable|url|max:500',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'konten.required' => 'Isi modul pembelajaran wajib diisi.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 10MB.',
        ]);

        $kategoriName = 'Umum';
        if ($request->id_kategori) {
            $cat = Category::find($request->id_kategori);
            if ($cat) $kategoriName = $cat->nama_kategori;
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('elearning_thumbnails', 'public');
        }

        $slug = Str::slug($request->judul) . '-' . time();
        $ringkasan = Str::limit(strip_tags($request->konten), 180);

        Elearning::create([
            'judul' => $request->judul,
            'slug' => $slug,
            'kategori' => $kategoriName,
            'level' => 'Pemula',
            'durasi' => '15 Menit',
            'thumbnail_url' => $thumbnailPath ? asset('storage/' . $thumbnailPath) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80',
            'ringkasan' => $ringkasan,
            'konten' => $request->konten,
            'video_url' => $request->video_url,
            'view_count' => 0,
        ]);

        return redirect()->route('admin.elearning')->with('success', 'Materi E-Learning baru berhasil dipublikasikan!');
    }

    /**
     * Tampilkan Halaman Form Edit Materi E-Learning.
     */
    public function edit($id)
    {
        $material = Elearning::findOrFail($id);
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('admin.elearning.edit', compact('material', 'categories'));
    }

    /**
     * Perbarui Materi E-Learning.
     */
    public function update(Request $request, $id)
    {
        $material = Elearning::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'id_kategori' => 'nullable|exists:categories,id',
            'video_url' => 'nullable|url|max:500',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'konten.required' => 'Isi modul pembelajaran wajib diisi.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 10MB.',
        ]);

        $kategoriName = $material->kategori ?: 'Umum';
        if ($request->id_kategori) {
            $cat = Category::find($request->id_kategori);
            if ($cat) $kategoriName = $cat->nama_kategori;
        }

        $thumbnailUrl = $material->thumbnail_url;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('elearning_thumbnails', 'public');
            $thumbnailUrl = asset('storage/' . $thumbnailPath);
        }

        $ringkasan = Str::limit(strip_tags($request->konten), 180);

        $material->update([
            'judul' => $request->judul,
            'kategori' => $kategoriName,
            'thumbnail_url' => $thumbnailUrl,
            'ringkasan' => $ringkasan,
            'konten' => $request->konten,
            'video_url' => $request->video_url,
        ]);

        return redirect()->route('admin.elearning')->with('success', 'Materi E-Learning berhasil diperbarui!');
    }

    /**
     * Hapus Materi E-Learning.
     */
    public function destroy($id)
    {
        $material = Elearning::findOrFail($id);
        $material->delete();

        return redirect()->route('admin.elearning')->with('success', 'Materi E-Learning berhasil dihapus.');
    }
}
