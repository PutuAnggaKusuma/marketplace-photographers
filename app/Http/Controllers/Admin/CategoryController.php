<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Tampilkan Tabel Pengelolaan Master Kategori Admin.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $query = Category::orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        $categories = $query->paginate(10)->withQueryString();
        $totalCategories = Category::count();

        return view('admin.categories.index', compact('categories', 'search', 'totalCategories'));
    }

    /**
     * Simpan Master Kategori Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori',
            'deskripsi' => 'nullable|string|max:500',
            'icon_url' => 'nullable|url',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
        ]);

        $slug = Str::slug($request->nama_kategori);

        Category::create([
            'nama_kategori' => trim($request->nama_kategori),
            'slug' => $slug,
            'deskripsi' => trim($request->deskripsi),
            'icon_url' => trim($request->icon_url) ?: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80',
        ]);

        return redirect()->back()->with('success', "Kategori '{$request->nama_kategori}' berhasil ditambahkan!");
    }

    /**
     * Perbarui Master Kategori.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string|max:500',
            'icon_url' => 'nullable|url',
        ]);

        $slug = Str::slug($request->nama_kategori);

        $category->update([
            'nama_kategori' => trim($request->nama_kategori),
            'slug' => $slug,
            'deskripsi' => trim($request->deskripsi),
            'icon_url' => trim($request->icon_url) ?: $category->icon_url,
        ]);

        return redirect()->back()->with('success', 'Master kategori berhasil diperbarui!');
    }

    /**
     * Hapus Master Kategori.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Master kategori berhasil dihapus.');
    }
}