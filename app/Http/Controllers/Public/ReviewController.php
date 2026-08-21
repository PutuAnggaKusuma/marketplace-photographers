<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\RoleClient;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Simpan Ulasan & Rating Baru dari Klien.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $client = RoleClient::where('id_user', $user->id)->first();
        if (!$client) {
            return redirect()->back()->with('error', 'Hanya akun Klien yang dapat memberikan ulasan.');
        }

        $request->validate([
            'id_contract' => 'required|exists:contracts,id',
            'rating' => 'required|integer|min:1|max:5',
            'deskripsi_review' => 'required|string|min:5|max:1000',
        ], [
            'rating.required' => 'Silakan pilih bintang rating (1 - 5).',
            'deskripsi_review.required' => 'Tuliskan ulasan testimoni Anda.',
            'deskripsi_review.min' => 'Ulasan minimal 5 karakter.',
        ]);

        $contract = Contract::with(['bookingDetail'])->findOrFail($request->id_contract);

        // Verifikasi kepemilikan kontrak
        if ((int) $contract->id_client !== (int) $client->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengulas pesanan ini.');
        }

        // Verifikasi status selesai
        $isCompleted = $contract->status_contract === 'completed' || 
                       ($contract->bookingDetail && $contract->bookingDetail->status_booking === 'completed');

        if (!$isCompleted) {
            return redirect()->back()->with('error', 'Ulasan hanya dapat diberikan setelah sesi foto selesai.');
        }

        // Check if already reviewed
        $existing = Testimonial::where('id_contract', $contract->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        Testimonial::create([
            'id_client' => $client->id,
            'id_photographer' => $contract->id_photographer,
            'id_contract' => $contract->id,
            'rating' => (int) $request->rating,
            'deskripsi_review' => trim($request->deskripsi_review),
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Ulasan dan rating Anda berhasil disimpan.');
    }
}