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
            'is_premium' => 'nullable|in:0,1',
            'harga' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|url|max:500',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'konten.required' => 'Isi modul pembelajaran wajib diisi.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format thumbnail harus JPG, PNG, atau WEBP.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 10 MB.',
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
        $isPremium = $request->input('is_premium') == '1';
        $harga = $isPremium ? (int) $request->input('harga', 0) : 0;

        Elearning::create([
            'judul' => $request->judul,
            'slug' => $slug,
            'kategori' => $kategoriName,
            'level' => 'Pemula',
            'durasi' => '15 Menit',
            'harga' => $harga,
            'is_premium' => $isPremium,
            'thumbnail_url' => $thumbnailPath ? asset('storage/' . $thumbnailPath) : null,
            'ringkasan' => $ringkasan,
            'konten' => $request->konten,
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
            'is_premium' => 'nullable|in:0,1',
            'harga' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|url|max:500',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'konten.required' => 'Isi modul pembelajaran wajib diisi.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format thumbnail harus JPG, PNG, atau WEBP.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 10 MB.',
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
        $isPremium = $request->input('is_premium') == '1';
        $harga = $isPremium ? (int) $request->input('harga', 0) : 0;

        $material->update([
            'judul' => $request->judul,
            'kategori' => $kategoriName,
            'harga' => $harga,
            'is_premium' => $isPremium,
            'thumbnail_url' => $thumbnailUrl,
            'ringkasan' => $ringkasan,
            'konten' => $request->konten,
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

    /**
     * Halaman Kelola Kurikulum, Topik & Tahapan Belajar E-Learning.
     */
    public function curriculum($id)
    {
        $course = Elearning::with(['modules.lessons', 'lessons'])->findOrFail($id);
        return view('admin.elearning.curriculum', compact('course'));
    }

    /**
     * Simpan Topik/Modul Baru.
     */
    public function storeModule(Request $request, $id)
    {
        $course = Elearning::findOrFail($id);

        $request->validate([
            'judul_modul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:1',
        ]);

        $maxOrder = $course->modules()->max('urutan') ?? 0;

        \App\Models\ElearningModule::create([
            'id_elearning' => $course->id,
            'judul_modul' => $request->judul_modul,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->urutan ?: ($maxOrder + 1),
        ]);

        return redirect()->route('admin.elearning.curriculum', $course->id)->with('success', 'Topik modul baru berhasil ditambahkan!');
    }

    /**
     * Perbarui Topik/Modul.
     */
    public function updateModule(Request $request, $id)
    {
        $module = \App\Models\ElearningModule::findOrFail($id);

        $request->validate([
            'judul_modul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:1',
        ]);

        $module->update([
            'judul_modul' => $request->judul_modul,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->urutan ?? $module->urutan,
        ]);

        return redirect()->route('admin.elearning.curriculum', $module->id_elearning)->with('success', 'Topik modul berhasil diperbarui.');
    }

    /**
     * Hapus Topik/Modul.
     */
    public function destroyModule($id)
    {
        $module = \App\Models\ElearningModule::findOrFail($id);
        $courseId = $module->id_elearning;
        $module->delete();

        return redirect()->route('admin.elearning.curriculum', $courseId)->with('success', 'Topik modul berhasil dihapus.');
    }

    /**
     * Simpan Tahapan Belajar (Lesson) Baru.
     */
    public function storeLesson(Request $request, $id)
    {
        $course = Elearning::findOrFail($id);

        $request->validate([
            'id_module' => 'nullable|exists:elearning_modules,id',
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:video,reading,audio,foto',
            'konten' => 'nullable|string',
            'media_url' => 'nullable|string|max:1000',
            'durasi_menit' => 'nullable|integer|min:1',
            'urutan' => 'nullable|integer|min:1',
            'is_free' => 'nullable|in:0,1',
            'media_file' => 'nullable|file|max:51200', // max 50MB
        ], [
            'judul.required' => 'Judul tahapan materi wajib diisi.',
            'media_file.file' => 'File media tidak valid.',
            'media_file.max' => 'Ukuran file media maksimal 50 MB.',
        ]);

        $mediaPath = null;
        $mediaType = 'url';

        if ($request->hasFile('media_file')) {
            $mediaPath = $request->file('media_file')->store('elearning_media', 'public');
            $mediaType = 'upload';
        } elseif ($request->media_url) {
            if (str_contains($request->media_url, 'youtube.com') || str_contains($request->media_url, 'youtu.be')) {
                $mediaType = 'youtube';
            } else {
                $mediaType = 'external';
            }
        }

        $maxOrder = $course->lessons()->max('urutan') ?? 0;

        \App\Models\ElearningLesson::create([
            'id_elearning' => $course->id,
            'id_module' => $request->id_module ?: null,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'konten' => $request->konten,
            'media_type' => $mediaType,
            'media_url' => $request->media_url,
            'media_path' => $mediaPath,
            'durasi_menit' => $request->durasi_menit ?? 5,
            'urutan' => $request->urutan ?: ($maxOrder + 1),
            'is_free' => $request->has('is_free') ? (bool) $request->is_free : true,
        ]);

        return redirect()->route('admin.elearning.curriculum', $course->id)->with('success', 'Tahapan materi belajar baru berhasil ditambahkan!');
    }

    /**
     * Perbarui Tahapan Belajar (Lesson).
     */
    public function updateLesson(Request $request, $id)
    {
        $lesson = \App\Models\ElearningLesson::findOrFail($id);

        $request->validate([
            'id_module' => 'nullable|exists:elearning_modules,id',
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:video,reading,audio,foto',
            'konten' => 'nullable|string',
            'media_url' => 'nullable|string|max:1000',
            'durasi_menit' => 'nullable|integer|min:1',
            'urutan' => 'nullable|integer|min:1',
            'is_free' => 'nullable|in:0,1',
            'media_file' => 'nullable|file|max:51200',
        ], [
            'judul.required' => 'Judul tahapan materi wajib diisi.',
            'media_file.file' => 'File media tidak valid.',
            'media_file.max' => 'Ukuran file media maksimal 50 MB.',
        ]);

        $mediaPath = $lesson->media_path;
        $mediaType = $lesson->media_type;

        if ($request->hasFile('media_file')) {
            $mediaPath = $request->file('media_file')->store('elearning_media', 'public');
            $mediaType = 'upload';
        } elseif ($request->media_url) {
            if (str_contains($request->media_url, 'youtube.com') || str_contains($request->media_url, 'youtu.be')) {
                $mediaType = 'youtube';
            } else {
                $mediaType = 'external';
            }
        }

        $lesson->update([
            'id_module' => $request->id_module ?: $lesson->id_module,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'konten' => $request->konten,
            'media_type' => $mediaType,
            'media_url' => $request->media_url,
            'media_path' => $mediaPath,
            'durasi_menit' => $request->durasi_menit ?? $lesson->durasi_menit,
            'urutan' => $request->urutan ?? $lesson->urutan,
            'is_free' => $request->has('is_free') ? (bool) $request->is_free : $lesson->is_free,
        ]);

        return redirect()->route('admin.elearning.curriculum', $lesson->id_elearning)->with('success', 'Tahapan materi berhasil diperbarui.');
    }

    /**
     * Hapus Tahapan Belajar (Lesson).
     */
    public function destroyLesson($id)
    {
        $lesson = \App\Models\ElearningLesson::findOrFail($id);
        $courseId = $lesson->id_elearning;
        $lesson->delete();

        return redirect()->route('admin.elearning.curriculum', $courseId)->with('success', 'Tahapan materi berhasil dihapus.');
    }

    /**
     * Toggle Cepat Status Free Preview vs Berbayar.
     */
    public function toggleLessonFree(Request $request, $id)
    {
        $lesson = \App\Models\ElearningLesson::findOrFail($id);
        $lesson->is_free = !$lesson->is_free;
        $lesson->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_free' => $lesson->is_free,
                'message' => 'Status akses tahapan berhasil diperbarui menjadi ' . ($lesson->is_free ? 'Gratis (Free Preview)' : 'Berbayar (Locked)'),
            ]);
        }

        return redirect()->back()->with('success', 'Status akses tahapan berhasil diubah.');
    }
}
