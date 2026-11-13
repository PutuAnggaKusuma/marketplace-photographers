<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PhotographerService;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class OnboardingController extends Controller
{
    /**
     * Selesaikan Onboarding 3 Langkah Fotografer.
     */
    public function complete(Request $request)
    {
        $request->validate([
            // Langkah 1: Domisili & Kategori Spesialisasi
            'province_code' => ['required', 'string'],
            'city_code' => ['required', 'string'],
            'alamat' => ['required', 'string', 'max:500'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'exists:categories,id'],

            // Langkah 2: Portofolio / Sosmed & Paket Layanan Pertama
            'link_sosmed' => ['required', 'string', 'max:255'],
            'deskripsi_bio' => ['required', 'string', 'min:15'],
            'nama_layanan' => ['required', 'string', 'max:150'],
            'tarif_harga' => ['required', 'numeric', 'min:10000'],
            'deskripsi_layanan' => ['required', 'string', 'min:10'],

            // Langkah 3: Rekening Bank Payout
            'nama_bank' => ['required', 'string', 'max:50'],
            'nomor_rekening' => ['required', 'string', 'max:50'],
            'atas_nama_rekening' => ['required', 'string', 'max:150'],
        ], [
            'province_code.required' => 'Provinsi domisili wajib dipilih.',
            'city_code.required' => 'Kota/Kabupaten domisili wajib dipilih.',
            'alamat.required' => 'Alamat lengkap atau basis studio wajib diisi.',
            'categories.required' => 'Pilih minimal 1 kategori spesialisasi foto.',
            'categories.min' => 'Pilih minimal 1 kategori spesialisasi foto.',
            'link_sosmed.required' => 'Tautan Instagram / Portofolio online wajib diisi.',
            'deskripsi_bio.required' => 'Bio singkat perkenalan fotografer wajib diisi.',
            'deskripsi_bio.min' => 'Bio perkenalan minimal 15 karakter.',
            'nama_layanan.required' => 'Nama paket layanan perdana wajib diisi.',
            'tarif_harga.required' => 'Tarif per jam paket perdana wajib diisi.',
            'tarif_harga.min' => 'Tarif per jam minimal Rp 10.000.',
            'deskripsi_layanan.required' => 'Uraian fasilitas paket layanan perdana wajib diisi.',
            'deskripsi_layanan.min' => 'Deskripsi layanan minimal 10 karakter.',
            'nama_bank.required' => 'Nama bank pencairan wajib dipilih atau diisi.',
            'nomor_rekening.required' => 'Nomor rekening bank wajib diisi.',
            'atas_nama_rekening.required' => 'Nama pemilik rekening bank wajib diisi.',
        ]);

        $photographer = RolePhotographer::where('id_user', Auth::id())->firstOrFail();

        DB::transaction(function () use ($photographer, $request) {
            // 1. Update profil fotografer dengan data domisili, sosmed, rekening, dan tandai is_onboarded = true
            $photographer->update([
                'province_code' => $request->province_code,
                'city_code' => $request->city_code,
                'alamat' => $request->alamat,
                'link_sosmed' => $request->link_sosmed,
                'deskripsi_bio' => $request->deskripsi_bio,
                'nama_bank' => $request->nama_bank,
                'nomor_rekening' => $request->nomor_rekening,
                'atas_nama_rekening' => $request->atas_nama_rekening,
                'is_onboarded' => true,
            ]);

            // 2. Hubungkan kategori spesialisasi terpilih (pertahankan kategori yang sedang dipakai portofolio)
            $usedCategoryIds = array_keys($photographer->getUsedPortfolioCategoryCounts());
            $categoriesToSync = array_values(array_unique(array_merge(
                array_map('intval', $request->categories ?? []),
                $usedCategoryIds
            )));
            $photographer->categories()->sync($categoriesToSync);

            // 3. Terbitkan paket layanan perdana
            PhotographerService::create([
                'id_photographer' => $photographer->id,
                'nama_layanan' => $request->nama_layanan,
                'tarif_harga' => $request->tarif_harga,
                'deskripsi_layanan' => $request->deskripsi_layanan,
                'spare_time_enabled' => false,
                'spare_before_minutes' => 0,
                'spare_after_minutes' => 0,
                'allow_same_location_bypass' => false,
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data profil fotografer berhasil dikirim untuk verifikasi Admin.',
                'redirect' => route('photographer.dashboard'),
            ]);
        }

        return redirect()->route('photographer.dashboard')
            ->with('success', 'Data profil fotografer berhasil dikirim untuk verifikasi Admin.');
    }
}

