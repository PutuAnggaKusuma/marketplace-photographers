<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerPortfolio;
use App\Models\PortfolioMedia;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PortfolioController extends Controller
{
    /**
     * Tampilkan Halaman Form Tambah Portofolio Karya Baru.
     */
    public function create()
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);
        $categories = $photographer->categories()->orderBy('nama_kategori', 'asc')->get();

        return view('photographer.portfolio.create', compact('photographer', 'categories'));
    }

    /**
     * Tampilkan Halaman Form Edit Portofolio Karya.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $portfolio = PhotographerPortfolio::with(['medias', 'category'])
            ->where('id_photographer', $photographer->id)
            ->findOrFail($id);
        $categories = $photographer->categories()->orderBy('nama_kategori', 'asc')->get();

        return view('photographer.portfolio.edit', compact('photographer', 'portfolio', 'categories'));
    }

    /**
     * Tampilkan Halaman Pengelolaan Portofolio & Galeri Karya Fotografer.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $search = $request->query('search', '');
        $sort = $request->query('sort', 'latest');
        $query = PhotographerPortfolio::with(['medias', 'category'])->where('id_photographer', $photographer->id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif (in_array($sort, ['name_asc', 'title_asc'], true)) {
            $query->orderBy('judul', 'asc');
        } elseif (in_array($sort, ['name_desc', 'title_desc'], true)) {
            $query->orderBy('judul', 'desc');
        } elseif ($sort === 'most_media') {
            $query->withCount('medias')->orderByDesc('medias_count');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $portfolios = $query->paginate($perPage)->withQueryString();

        // Metrics
        $allPortfolios = PhotographerPortfolio::with(['medias', 'category'])
            ->where('id_photographer', $photographer->id)
            ->get();
        $totalPortfolios = $allPortfolios->count();
        $totalMedias = PortfolioMedia::whereIn('id_portofolio', $allPortfolios->pluck('id'))->count();
        $avgMediaPerAlbum = $totalPortfolios > 0 ? round($totalMedias / $totalPortfolios, 1) : 0;
        $latestPortfolio = $allPortfolios->sortByDesc('created_at')->first();

        $categoryCounts = [];
        $categoryNamesById = [];
        foreach ($allPortfolios as $item) {
            if ($item->id_kategori && $item->category) {
                $catId = (int) $item->id_kategori;
                $categoryCounts[$catId] = ($categoryCounts[$catId] ?? 0) + 1;
                $categoryNamesById[$catId] = $item->category->nama_kategori;
            }
        }

        $topCategoryName = null;
        if (!empty($categoryCounts)) {
            uksort($categoryCounts, function ($a, $b) use ($categoryCounts, $categoryNamesById) {
                $cmp = $categoryCounts[$b] <=> $categoryCounts[$a];
                if ($cmp !== 0) {
                    return $cmp;
                }
                return strcmp($categoryNamesById[$a] ?? '', $categoryNamesById[$b] ?? '');
            });
            $topCatId = array_key_first($categoryCounts);
            $topCategoryName = $categoryNamesById[$topCatId] ?? null;
        }

        if (!$topCategoryName) {
            $topCategoryName = $photographer->categories()->orderBy('nama_kategori', 'asc')->value('nama_kategori') ?: '-';
        }

        $uniqueCategoryCount = count($categoryCounts);

        return view('photographer.portfolio.index', compact(
            'photographer',
            'portfolios',
            'search',
            'sort',
            'totalPortfolios',
            'totalMedias',
            'avgMediaPerAlbum',
            'latestPortfolio',
            'topCategoryName',
            'uniqueCategoryCount'
        ));
    }

    /**
     * Simpan Album Portofolio Baru Beserta Foto-Fotonya (Minimal 4 Foto Standar Platform).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);
        $allowedCategoryIds = $photographer->categories()->pluck('categories.id')->map(fn($id) => (int) $id)->toArray();

        $request->validate([
            'judul' => 'required|string|min:3|max:255',
            'id_kategori' => ['required', 'integer', Rule::in($allowedCategoryIds)],
            'deskripsi' => 'required|string|min:15|max:1000',
            'foto_files' => 'required|array|min:4',
            'foto_files.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:20480',
        ], [
            'judul.required' => 'Judul portofolio wajib diisi.',
            'judul.min' => 'Judul portofolio minimal 3 karakter.',
            'id_kategori.required' => 'Kategori portofolio wajib dipilih.',
            'id_kategori.integer' => 'Kategori portofolio tidak valid.',
            'id_kategori.in' => empty($allowedCategoryIds)
                ? 'Pilih kategori di profil terlebih dahulu.'
                : 'Kategori harus sesuai dengan kategori di profil.',
            'deskripsi.required' => 'Deskripsi portofolio wajib diisi.',
            'deskripsi.min' => 'Deskripsi portofolio minimal 15 karakter agar informatif bagi calon klien.',
            'foto_files.required' => 'Wajib mengunggah minimal 4 foto untuk membuat 1 album portofolio baru.',
            'foto_files.array' => 'Format file foto harus berupa kumpulan gambar.',
            'foto_files.min' => 'Wajib mengunggah minimal 4 foto untuk membuat 1 album portofolio baru sesuai standar platform.',
            'foto_files.*.required' => 'File foto tidak boleh kosong.',
            'foto_files.*.image' => 'Setiap file harus berupa gambar.',
            'foto_files.*.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto_files.*.max' => 'Ukuran setiap file foto maksimal 20 MB.',
        ]);

        $portfolio = PhotographerPortfolio::create([
            'id_photographer' => $photographer->id,
            'id_kategori' => (int) $request->id_kategori,
            'judul' => trim($request->judul),
            'deskripsi' => trim($request->deskripsi),
        ]);

        if ($request->hasFile('foto_files')) {
            foreach ($request->file('foto_files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('portfolios', $filename, 'public');

                // Terapkan auto-watermarking proteksi karya publik
                $storagePath = storage_path('app/public/portfolios/' . $filename);
                \App\Services\WatermarkService::applyWatermark($storagePath, $photographer->nama ?? 'LensMatch Verified');

                $url = asset('storage/portfolios/' . $filename);

                PortfolioMedia::create([
                    'id_portofolio' => $portfolio->id,
                    'media' => $url,
                ]);
            }
        }

        return redirect()->route('photographer.portfolio')->with('success', 'Album portofolio karya berhasil ditambahkan dengan ' . count($request->file('foto_files')) . ' foto!');
    }

    /**
     * Perbarui Album Portofolio (Memastikan Minimal 4 Foto).
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $portfolio = PhotographerPortfolio::where('id_photographer', $photographer->id)->findOrFail($id);
        $allowedCategoryIds = $photographer->categories()->pluck('categories.id')->map(fn($id) => (int) $id)->toArray();

        $request->validate([
            'judul' => 'required|string|min:3|max:255',
            'id_kategori' => ['required', 'integer', Rule::in($allowedCategoryIds)],
            'deskripsi' => 'required|string|min:15|max:1000',
            'foto_files' => 'nullable|array',
            'foto_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
        ], [
            'judul.required' => 'Judul album portofolio wajib diisi.',
            'judul.min' => 'Judul album portofolio minimal 3 karakter.',
            'id_kategori.required' => 'Kategori portofolio wajib dipilih.',
            'id_kategori.integer' => 'Kategori portofolio tidak valid.',
            'id_kategori.in' => empty($allowedCategoryIds)
                ? 'Pilih kategori di profil terlebih dahulu.'
                : 'Kategori harus sesuai dengan kategori di profil.',
            'deskripsi.required' => 'Deskripsi portofolio wajib diisi.',
            'deskripsi.min' => 'Deskripsi portofolio minimal 15 karakter.',
            'foto_files.*.image' => 'Setiap file harus berupa gambar.',
            'foto_files.*.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto_files.*.max' => 'Ukuran setiap file foto maksimal 20 MB.',
        ]);

        $currentCount = $portfolio->medias()->count();
        $newFilesCount = $request->hasFile('foto_files') ? count($request->file('foto_files')) : 0;
        if (($currentCount + $newFilesCount) < 4) {
            return back()->withErrors(['foto_files' => 'Setiap album portofolio wajib memiliki minimal 4 foto (total saat ini: ' . ($currentCount + $newFilesCount) . ' foto).'])->withInput();
        }

        $portfolio->update([
            'id_kategori' => (int) $request->id_kategori,
            'judul' => trim($request->judul),
            'deskripsi' => trim($request->deskripsi),
        ]);

        if ($request->hasFile('foto_files')) {
            foreach ($request->file('foto_files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('portfolios', $filename, 'public');

                // Terapkan auto-watermarking proteksi karya publik
                $storagePath = storage_path('app/public/portfolios/' . $filename);
                \App\Services\WatermarkService::applyWatermark($storagePath, $photographer->nama ?? 'LensMatch Verified');

                $url = asset('storage/portfolios/' . $filename);

                PortfolioMedia::create([
                    'id_portofolio' => $portfolio->id,
                    'media' => $url,
                ]);
            }
        }

        return redirect()->route('photographer.portfolio')->with('success', 'Album portofolio berhasil diperbarui!');
    }

    /**
     * Hapus Album Portofolio & Medias.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $portfolio = PhotographerPortfolio::where('id_photographer', $photographer->id)->findOrFail($id);
        PortfolioMedia::where('id_portofolio', $portfolio->id)->delete();
        $portfolio->delete();

        $downgraded = $photographer->fresh()->syncStorefrontIntegrity();
        $message = 'Album portofolio berhasil dihapus!';
        if ($downgraded) {
            $message .= ' Etalase dinonaktifkan sementara hingga minimal satu album portofolio tersedia kembali.';
        }

        return redirect()->route('photographer.portfolio')->with('success', $message);
    }
}