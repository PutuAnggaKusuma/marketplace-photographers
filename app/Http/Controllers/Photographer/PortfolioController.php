<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerPortfolio;
use App\Models\PortfolioMedia;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    /**
     * Tampilkan Halaman Form Tambah Portofolio Karya Baru.
     */
    public function create()
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) $photographer = RolePhotographer::first();
        $categories = \App\Models\Category::orderBy('nama_kategori', 'asc')->get();

        return view('photographer.portfolio.create', compact('photographer', 'categories'));
    }

    /**
     * Tampilkan Halaman Form Edit Portofolio Karya.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) $photographer = RolePhotographer::first();

        $portfolio = \App\Models\PhotographerPortfolio::where('id_photographer', $photographer->id)->findOrFail($id);
        $categories = \App\Models\Category::orderBy('nama_kategori', 'asc')->get();

        return view('photographer.portfolio.edit', compact('photographer', 'portfolio', 'categories'));
    }

    /**
     * Tampilkan Halaman Pengelolaan Portofolio & Galeri Karya Fotografer.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $search = $request->query('search', '');
        $sort = $request->query('sort', 'latest');
        $query = PhotographerPortfolio::with('medias')->where('id_photographer', $photographer->id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('judul', 'asc');
        } elseif ($sort === 'name_desc') {
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
        $allPortfolios = PhotographerPortfolio::with('medias')->where('id_photographer', $photographer->id)->get();
        $totalPortfolios = $allPortfolios->count();
        $totalMedias = PortfolioMedia::whereIn('id_portofolio', $allPortfolios->pluck('id'))->count();
        $avgMediaPerAlbum = $totalPortfolios > 0 ? round($totalMedias / $totalPortfolios, 1) : 0;
        $latestPortfolio = $allPortfolios->sortByDesc('created_at')->first();

        return view('photographer.portfolio.index', compact(
            'photographer',
            'portfolios',
            'search',
            'sort',
            'totalPortfolios',
            'totalMedias',
            'avgMediaPerAlbum',
            'latestPortfolio'
        ));
    }

    /**
     * Simpan Album Portofolio Baru Beserta Foto-Fotonya.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:1000',
            'foto_files' => 'nullable|array',
            'foto_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:204800',
        ], [
            'judul.required' => 'Judul album portofolio wajib diisi.',
            'deskripsi.required' => 'Deskripsi portofolio wajib diisi.',
            'foto_files.*.image' => 'File harus berupa format gambar valid (JPG, PNG, WEBP).',
            'foto_files.*.max' => 'Ukuran file foto maksimal 200MB.',
        ]);

        $portfolio = PhotographerPortfolio::create([
            'id_photographer' => $photographer->id,
            'judul' => trim($request->judul),
            'deskripsi' => trim($request->deskripsi),
        ]);

        if ($request->hasFile('foto_files')) {
            foreach ($request->file('foto_files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('portfolios', $filename, 'public');
                $url = asset('storage/portfolios/' . $filename);

                PortfolioMedia::create([
                    'id_portofolio' => $portfolio->id,
                    'media' => $url,
                ]);
            }
        }

        return redirect()->route('photographer.portfolio')->with('success', 'Album portofolio karya berhasil ditambahkan!');
    }

    /**
     * Perbarui Album Portofolio.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $portfolio = PhotographerPortfolio::where('id_photographer', $photographer->id)->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:1000',
            'foto_files' => 'nullable|array',
            'foto_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:204800',
        ]);

        $portfolio->update([
            'judul' => trim($request->judul),
            'deskripsi' => trim($request->deskripsi),
        ]);

        if ($request->hasFile('foto_files')) {
            foreach ($request->file('foto_files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('portfolios', $filename, 'public');
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
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $portfolio = PhotographerPortfolio::where('id_photographer', $photographer->id)->findOrFail($id);
        PortfolioMedia::where('id_portofolio', $portfolio->id)->delete();
        $portfolio->delete();

        return redirect()->route('photographer.portfolio')->with('success', 'Album portofolio berhasil dihapus!');
    }
}