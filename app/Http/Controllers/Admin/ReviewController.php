<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Tampilkan Dashboard Moderasi Ulasan & Rating Admin.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $ratingFilter = $request->query('rating', 'all');

        $query = Testimonial::with(['client.user', 'photographer.user'])->orderBy('created_at', 'desc');

        if ($ratingFilter && $ratingFilter !== 'all') {
            if ($ratingFilter === '5') {
                $query->where('rating', 5);
            } elseif ($ratingFilter === '4') {
                $query->where('rating', 4);
            } elseif ($ratingFilter === 'low') {
                $query->where('rating', '<=', 3);
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi_review', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('photographer', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->paginate(10)->withQueryString();

        $totalReviews = Testimonial::count();
        $avgRating = number_format(Testimonial::avg('rating') ?: 5.0, 1);
        $fiveStarReviews = Testimonial::where('rating', 5)->count();

        return view('admin.reviews.index', compact(
            'reviews', 'search', 'ratingFilter',
            'totalReviews', 'avgRating', 'fiveStarReviews'
        ));
    }

    /**
     * Toggle Status Sembunyikan / Publikasikan Ulasan.
     */
    public function toggleHide(Request $request, $id)
    {
        $review = Testimonial::findOrFail($id);
        $newStatus = !$review->is_hidden;
        $review->update(['is_hidden' => $newStatus]);

        $statusText = $newStatus ? 'disembunyikan dari publikasi' : 'dipublikasikan kembali';

        return redirect()->back()->with('success', "Status ulasan berhasil {$statusText}.");
    }

    /**
     * Hapus Ulasan Testimoni Spam.
     */
    public function destroy($id)
    {
        $review = Testimonial::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Ulasan testimoni berhasil dihapus.');
    }
}