<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PhotographerService;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Tampilkan Halaman Form Tambah Kategori Baru.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Tampilkan Halaman Form Edit Kategori.
     */
    public function edit($id)
    {
        $category = \App\Models\Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Tampilkan Tabel Pengelolaan Master Kategori Admin.
     */
        public function index(Request $request)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'name_asc');
        $query = Category::withCount('photographers');

        if (!empty($search)) {
            $query->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        if ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('nama_kategori', 'desc');
        } elseif ($sort === 'most_photographers') {
            $query->orderByDesc('photographers_count');
        } else {
            $query->orderBy('nama_kategori', 'asc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $categories = $query->paginate($perPage)->withQueryString();
        $totalCategories = Category::count();
        $totalMappedPhotographers = RolePhotographer::has('categories')->count();
        $totalServices = PhotographerService::count();
        $topCategory = Category::withCount('photographers')->orderByDesc('photographers_count')->first();

        return view('admin.categories.index', compact('categories', 'search', 'sort', 'totalCategories', 'totalMappedPhotographers', 'totalServices', 'topCategory'));
    }

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