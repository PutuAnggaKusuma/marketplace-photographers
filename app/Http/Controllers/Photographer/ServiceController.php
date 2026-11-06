<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerService;
use App\Models\RolePhotographer;
use App\Models\ServiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Tampilkan Halaman Form Buat Paket Layanan Baru.
     */
    public function create()
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        return view('photographer.services.create', compact('photographer'));
    }

    /**
     * Tampilkan Halaman Form Edit Paket Layanan.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $service = PhotographerService::with('details')
            ->where('id_photographer', $photographer->id)
            ->findOrFail($id);

        return view('photographer.services.edit', compact('photographer', 'service'));
    }

    /**
     * Tampilkan Halaman Pengelolaan Paket Layanan & Tarif Fotografer (Tabel Standar Dashboard).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $search = $request->query('search', '');
        $sort = $request->query('sort', 'latest');
        $query = PhotographerService::with('details')->where('id_photographer', $photographer->id);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_layanan', 'like', "%{$search}%")
                  ->orWhere('deskripsi_layanan', 'like', "%{$search}%")
                  ->orWhereHas('details', function($dq) use ($search) {
                      $dq->where('nama_fitur', 'like', "%{$search}%");
                  });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('tarif_harga', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('tarif_harga', 'desc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('nama_layanan', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('nama_layanan', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $services = $query->paginate($perPage)->withQueryString();

        // Metrics calculations
        $allServices = PhotographerService::where('id_photographer', $photographer->id)->get();
        $totalServices = $allServices->count();
        $avgPrice = $totalServices > 0 ? (int) $allServices->avg('tarif_harga') : 0;
        $minPrice = $totalServices > 0 ? (int) $allServices->min('tarif_harga') : 0;
        $maxPrice = $totalServices > 0 ? (int) $allServices->max('tarif_harga') : 0;
        $totalFeatures = ServiceDetail::whereIn('id_p_service', $allServices->pluck('id'))->count();

        return view('photographer.services.index', compact(
            'photographer',
            'services',
            'search',
            'sort',
            'totalServices',
            'avgPrice',
            'minPrice',
            'maxPrice',
            'totalFeatures'
        ));
    }

    /**
     * Simpan Paket Layanan Baru Beserta Detail Fasilitasnya.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $request->validate([
            'nama_layanan' => 'required|string|min:3|max:100',
            'tarif_harga' => 'required|numeric|min:10000',
            'deskripsi_layanan' => 'required|string|min:15|max:1000',
            'fitur' => 'required|array|min:1',
            'fitur.*' => 'required|string|min:3|max:255',
        ], [
            'nama_layanan.required' => 'Nama paket layanan wajib diisi.',
            'nama_layanan.min' => 'Nama paket layanan minimal 3 karakter.',
            'nama_layanan.max' => 'Nama paket layanan maksimal 100 karakter.',
            'tarif_harga.required' => 'Tarif harga paket wajib diisi.',
            'tarif_harga.numeric' => 'Tarif harga harus berupa nominal angka valid.',
            'tarif_harga.min' => 'Tarif harga paket minimal Rp 10.000.',
            'deskripsi_layanan.required' => 'Deskripsi paket layanan wajib diisi.',
            'deskripsi_layanan.min' => 'Deskripsi paket layanan minimal 15 karakter agar informatif bagi calon klien.',
            'deskripsi_layanan.max' => 'Deskripsi paket layanan maksimal 1.000 karakter.',
            'fitur.required' => 'Wajib menyertakan minimal 1 rincian benefit/fasilitas paket layanan.',
            'fitur.min' => 'Wajib menyertakan minimal 1 rincian benefit/fasilitas paket layanan.',
            'fitur.*.required' => 'Rincian benefit/fasilitas paket tidak boleh kosong.',
            'fitur.*.min' => 'Rincian benefit/fasilitas minimal 3 karakter.',
            'fitur.*.max' => 'Rincian benefit/fasilitas maksimal 255 karakter.',
        ]);

        $validFiturs = array_values(array_filter(
            $request->input('fitur', []),
            fn($item) => !empty(trim($item ?? ''))
        ));

        if (empty($validFiturs)) {
            return back()->withErrors(['fitur' => 'Wajib menyertakan minimal 1 rincian benefit/fasilitas layanan yang terisi.'])->withInput();
        }

        $service = PhotographerService::create([
            'id_photographer' => $photographer->id,
            'nama_layanan' => trim($request->nama_layanan),
            'tarif_harga' => (int) $request->tarif_harga,
            'deskripsi_layanan' => trim($request->deskripsi_layanan),
        ]);

        foreach ($validFiturs as $item) {
            ServiceDetail::create([
                'id_p_service' => $service->id,
                'nama_fitur' => trim($item),
                'tarif_harga' => 0,
            ]);
        }

        return redirect()->route('photographer.services')->with('success', 'Paket layanan fotografi berhasil ditambahkan!');
    }

    /**
     * Perbarui Paket Layanan & Fasilitas.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $service = PhotographerService::where('id_photographer', $photographer->id)->findOrFail($id);

        $request->validate([
            'nama_layanan' => 'required|string|min:3|max:100',
            'tarif_harga' => 'required|numeric|min:10000',
            'deskripsi_layanan' => 'required|string|min:15|max:1000',
            'fitur' => 'required|array|min:1',
            'fitur.*' => 'required|string|min:3|max:255',
        ], [
            'nama_layanan.required' => 'Nama paket layanan wajib diisi.',
            'nama_layanan.min' => 'Nama paket layanan minimal 3 karakter.',
            'nama_layanan.max' => 'Nama paket layanan maksimal 100 karakter.',
            'tarif_harga.required' => 'Tarif harga paket wajib diisi.',
            'tarif_harga.numeric' => 'Tarif harga harus berupa nominal angka valid.',
            'tarif_harga.min' => 'Tarif harga paket minimal Rp 10.000.',
            'deskripsi_layanan.required' => 'Deskripsi paket layanan wajib diisi.',
            'deskripsi_layanan.min' => 'Deskripsi paket layanan minimal 15 karakter agar informatif bagi calon klien.',
            'deskripsi_layanan.max' => 'Deskripsi paket layanan maksimal 1.000 karakter.',
            'fitur.required' => 'Wajib menyertakan minimal 1 rincian benefit/fasilitas paket layanan.',
            'fitur.min' => 'Wajib menyertakan minimal 1 rincian benefit/fasilitas paket layanan.',
            'fitur.*.required' => 'Rincian benefit/fasilitas paket tidak boleh kosong.',
            'fitur.*.min' => 'Rincian benefit/fasilitas minimal 3 karakter.',
            'fitur.*.max' => 'Rincian benefit/fasilitas maksimal 255 karakter.',
        ]);

        $validFiturs = array_values(array_filter(
            $request->input('fitur', []),
            fn($item) => !empty(trim($item ?? ''))
        ));

        if (empty($validFiturs)) {
            return back()->withErrors(['fitur' => 'Wajib menyertakan minimal 1 rincian benefit/fasilitas layanan yang terisi.'])->withInput();
        }

        $service->update([
            'nama_layanan' => trim($request->nama_layanan),
            'tarif_harga' => (int) $request->tarif_harga,
            'deskripsi_layanan' => trim($request->deskripsi_layanan),
        ]);

        // Sync service details
        ServiceDetail::where('id_p_service', $service->id)->delete();
        foreach ($validFiturs as $item) {
            ServiceDetail::create([
                'id_p_service' => $service->id,
                'nama_fitur' => trim($item),
                'tarif_harga' => 0,
            ]);
        }

        return redirect()->route('photographer.services')->with('success', 'Paket layanan berhasil diperbarui!');
    }

    /**
     * Hapus Paket Layanan Fotografi.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $service = PhotographerService::where('id_photographer', $photographer->id)->findOrFail($id);

        $hasActiveBookings = \App\Models\ContractBooking::where('id_service', $service->id)
            ->whereIn('status_booking', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return back()->with('error', 'Paket layanan tidak dapat dihapus karena masih digunakan pada pesanan aktif (pending/confirmed).');
        }

        ServiceDetail::where('id_p_service', $service->id)->delete();
        $service->delete();

        return redirect()->route('photographer.services')->with('success', 'Paket layanan berhasil dihapus!');
    }
}