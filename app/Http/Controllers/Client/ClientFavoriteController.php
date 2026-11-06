<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientFavorite;
use App\Models\RoleClient;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientFavoriteController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Fotografer Favorit Klien.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $clientProfile = RoleClient::firstOrCreate(
            ['id_user' => $user->id],
            ['nama' => $user->nama ?? 'Client LensMatch']
        );

        $perPage = (int) $request->query('per_page', 9);
        if (!in_array($perPage, [9, 18, 36])) {
            $perPage = 9;
        }

        $favorites = $clientProfile->favoritePhotographers()
            ->with(['city', 'categories', 'portfolios.medias', 'testimonials', 'services'])
            ->latest('client_photographer_favorites.created_at')
            ->paginate($perPage)
            ->withQueryString();

        $totalFavorites = $clientProfile->favorites()->count();

        return view('client.favorites.index', compact('favorites', 'clientProfile', 'totalFavorites'));
    }

    /**
     * Toggle Simpan / Hapus Fotografer dari Favorit (Mendukung AJAX & Standar Form).
     */
    public function toggle(Request $request, $photographerId)
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'message' => 'Silakan masuk ke akun Anda terlebih dahulu untuk menyimpan fotografer ke favorit.',
                    'redirect' => route('login', ['redirect' => url()->previous()]),
                ], 401);
            }
            return redirect()->guest(route('login', ['redirect' => url()->previous()]))
                ->with('warning', 'Silakan masuk ke akun Anda terlebih dahulu untuk menyimpan fotografer ke favorit.');
        }

        $user = Auth::user();
        $photographer = RolePhotographer::findOrFail($photographerId);

        $clientProfile = RoleClient::firstOrCreate(
            ['id_user' => $user->id],
            ['nama' => $user->nama ?? 'Client LensMatch']
        );

        $existing = ClientFavorite::where('id_client', $clientProfile->id)
            ->where('id_photographer', $photographer->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorited = false;
            $message = "Fotografer {$photographer->nama} telah dihapus dari daftar favorit.";
        } else {
            ClientFavorite::create([
                'id_client' => $clientProfile->id,
                'id_photographer' => $photographer->id,
            ]);
            $isFavorited = true;
            $message = "Fotografer {$photographer->nama} berhasil disimpan ke daftar favorit!";
        }

        $totalFavorites = $clientProfile->favorites()->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'authenticated' => true,
                'is_favorited' => $isFavorited,
                'total_favorites' => $totalFavorites,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}

