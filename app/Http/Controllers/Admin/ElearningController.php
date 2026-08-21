<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Elearning;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ElearningController extends Controller
{
    /**
     * Tampilkan Tabel Pengelolaan Materi E-Learning Admin.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $query = Elearning::orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('ringkasan', 'like', "%{$search}%");
        }

        $elearnings = $query->paginate(10)->withQueryString();
        $totalCourses = Elearning::count();
        $totalViews = Elearning::sum('view_count');

        return view('admin.elearning.index', compact('elearnings', 'search', 'totalCourses', 'totalViews'));
    }

    /**
     * Simpan Materi E-Learning Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'level' => 'required|in:Pemula,Menengah,Mahir',
            'durasi' => 'required|string|max:50',
            'thumbnail_url' => 'nullable|url',
            'ringkasan' => 'required|string|max:500',
            'konten' => 'required|string',
        ]);

        $slug = Str::slug($request->judul) . '-' . time();

        Elearning::create([
            'judul' => $request->judul,
            'slug' => $slug,
            'kategori' => $request->kategori,
            'level' => $request->level,
            'durasi' => $request->durasi,
            'thumbnail_url' => $request->thumbnail_url ?: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80',
            'ringkasan' => $request->ringkasan,
            'konten' => $request->konten,
        ]);

        return redirect()->back()->with('success', 'Materi E-Learning baru berhasil ditambahkan!');
    }

    /**
     * Perbarui Materi E-Learning.
     */
    public function update(Request $request, $id)
    {
        $elearning = Elearning::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'level' => 'required|in:Pemula,Menengah,Mahir',
            'durasi' => 'required|string|max:50',
            'thumbnail_url' => 'nullable|url',
            'ringkasan' => 'required|string|max:500',
            'konten' => 'required|string',
        ]);

        $elearning->update([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'level' => $request->level,
            'durasi' => $request->durasi,
            'thumbnail_url' => $request->thumbnail_url ?: $elearning->thumbnail_url,
            'ringkasan' => $request->ringkasan,
            'konten' => $request->konten,
        ]);

        return redirect()->back()->with('success', 'Materi E-Learning berhasil diperbarui!');
    }

    /**
     * Hapus Materi E-Learning.
     */
    public function destroy($id)
    {
        $elearning = Elearning::findOrFail($id);
        $elearning->delete();

        return redirect()->back()->with('success', 'Materi E-Learning berhasil dihapus.');
    }
}