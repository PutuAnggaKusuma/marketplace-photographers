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
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
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
            'nama_kategori' => 'required|string|max:100|unique:categories,nama_kategori',
            'deskripsi' => 'nullable|string|max:500',
            'foto_kategori' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'icon_url' => 'nullable|url',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
            'foto_kategori.image' => 'Berkas foto harus berupa gambar.',
            'foto_kategori.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto_kategori.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $slug = Str::slug($request->nama_kategori);
        $iconUrl = trim($request->icon_url) ?: null;

        if ($request->hasFile('foto_kategori')) {
            $path = $request->file('foto_kategori')->store('categories', 'public');
            $iconUrl = asset('storage/' . $path);
        }

        Category::create([
            'nama_kategori' => trim($request->nama_kategori),
            'slug' => $slug,
            'deskripsi' => trim($request->deskripsi),
            'icon_url' => $iconUrl,
        ]);

        return redirect()->route('admin.categories')->with('success', "Kategori '{$request->nama_kategori}' berhasil ditambahkan!");
    }

    /**
     * Perbarui Master Kategori.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:categories,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string|max:500',
            'foto_kategori' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'icon_url' => 'nullable|url',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
            'foto_kategori.image' => 'Berkas foto harus berupa gambar.',
            'foto_kategori.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto_kategori.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $slug = Str::slug($request->nama_kategori);
        $iconUrl = $category->icon_url;

        // Opsi hapus foto jika diminta
        if ($request->boolean('hapus_foto')) {
            if ($category->icon_url && str_contains($category->icon_url, '/storage/categories/')) {
                $oldPath = str_replace(asset('storage/'), '', $category->icon_url);
                \Illuminate\Support\Facades\Storage::disk('public')->delete(ltrim($oldPath, '/'));
            }
            $iconUrl = null;
        }

        if ($request->hasFile('foto_kategori')) {
            // Hapus file lama jika tersimpan di disk lokal
            if ($category->icon_url && str_contains($category->icon_url, '/storage/categories/')) {
                $oldPath = str_replace(asset('storage/'), '', $category->icon_url);
                \Illuminate\Support\Facades\Storage::disk('public')->delete(ltrim($oldPath, '/'));
            }
            $path = $request->file('foto_kategori')->store('categories', 'public');
            $iconUrl = asset('storage/' . $path);
        } elseif ($request->filled('icon_url')) {
            $iconUrl = trim($request->icon_url);
        }

        $category->update([
            'nama_kategori' => trim($request->nama_kategori),
            'slug' => $slug,
            'deskripsi' => trim($request->deskripsi),
            'icon_url' => $iconUrl,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Master kategori berhasil diperbarui!');
    }

    /**
     * Hapus Master Kategori.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $photographerCount = $category->photographers()->count();
        $portfolioCount = $category->portfolios()->count();

        if ($photographerCount > 0 || $portfolioCount > 0) {
            return redirect()->back()->with('error', "Kategori '{$category->nama_kategori}' tidak dapat dihapus karena masih terhubung dengan {$photographerCount} fotografer dan {$portfolioCount} portofolio.");
        }

        // Hapus file foto dari disk jika ada
        if ($category->icon_url && str_contains($category->icon_url, '/storage/categories/')) {
            $oldPath = str_replace(asset('storage/'), '', $category->icon_url);
            \Illuminate\Support\Facades\Storage::disk('public')->delete(ltrim($oldPath, '/'));
        }

        $category->delete();

        return redirect()->back()->with('success', 'Master kategori berhasil dihapus.');
    }
}