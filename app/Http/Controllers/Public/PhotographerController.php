<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\RolePhotographer;
use App\Models\RoleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class PhotographerController extends Controller
{
    /**
     * Display Overview Landing Page of Photographers (Hero, Categories Grid, Featured Preview).
     */
    public function index(Request $request)
    {
        // Pure dynamic database categories with photos and descriptions
        $categories = Category::all();
        
        // Take top 4 featured photographers (with services, portfolios, testimonials) that have completed onboarding
        $featuredPhotographers = RolePhotographer::where('is_onboarded', true)->with([
            'user', 'province', 'city', 'categories',
            'services' => function ($q) {
                $q->whereNotNull('tarif_harga')
                  ->where('tarif_harga', '>', 0)
                  ->whereNotNull('deskripsi_layanan')
                  ->where('deskripsi_layanan', '!=', '')
                  ->with('details');
            },
            'portfolios.medias', 'testimonials'
        ])
            ->take(4)
            ->get();

        $totalPhotographers = RolePhotographer::count();
        $totalCategories = Category::count();

        return view('public.photographers.index', compact(
            'categories',
            'featuredPhotographers',
            'totalPhotographers',
            'totalCategories'
        ));
    }

    /**
     * Display Full Catalog of Photographers with Filters (Location, Category, Price, Search).
     */
    public function katalog(Request $request)
    {
        $categories = Category::all();
        $provinces = Province::orderBy('name', 'asc')->get();

        $selectedProvince = $request->input('province');
        $selectedCity = $request->input('city');
        $selectedCategory = $request->input('category');
        $searchKeyword = $request->input('q');
        $locationKeyword = $request->input('location');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $ratingMin = $request->input('rating_min');
        $selectedSort = $request->input('sort', 'rekomendasi');

        $sortLabels = [
            'rekomendasi' => 'Rekomendasi',
            'rating' => 'Rating Tertinggi',
            'harga_terendah' => 'Harga Terendah',
            'harga_tertinggi' => 'Harga Tertinggi',
            'terpopuler' => 'Paling Banyak Dipesan',
            'terbaru' => 'Terbaru Bergabung',
        ];

        // Cities list based on selected province or default top cities
        $cities = collect();
        if ($selectedProvince) {
            $cities = City::where('province_code', $selectedProvince)->orderBy('name', 'asc')->get();
        }

        $query = RolePhotographer::where('is_onboarded', true)->with([
            'user', 'province', 'city', 'categories',
            'services' => function ($q) {
                $q->whereNotNull('tarif_harga')
                  ->where('tarif_harga', '>', 0)
                  ->whereNotNull('deskripsi_layanan')
                  ->where('deskripsi_layanan', '!=', '')
                  ->with('details');
            },
            'portfolios.medias', 'testimonials'
        ]);

        // Subqueries for Real Sorting & Minimum Rating Filter
        $query->withAvg('testimonials', 'rating')
              ->withCount('testimonials')
              ->withCount('contracts')
              ->withMin(['services' => function ($sq) {
                  $sq->whereNotNull('tarif_harga')
                     ->where('tarif_harga', '>', 0)
                     ->whereNotNull('deskripsi_layanan')
                     ->where('deskripsi_layanan', '!=', '');
              }], 'tarif_harga');

        // Strict Location Search from Beranda (Matches ONLY Location, Address, Province, or City)
        if ($locationKeyword) {
            $matchingProvinceCodes = Province::where('name', 'LIKE', '%' . $locationKeyword . '%')->pluck('code');
            $matchingCityCodes = City::where('name', 'LIKE', '%' . $locationKeyword . '%')->pluck('code');

            $query->where(function ($q) use ($locationKeyword, $matchingProvinceCodes, $matchingCityCodes) {
                $q->where('alamat', 'LIKE', '%' . $locationKeyword . '%');

                if ($matchingProvinceCodes->isNotEmpty()) {
                    $q->orWhereIn('province_code', $matchingProvinceCodes);
                }

                if ($matchingCityCodes->isNotEmpty()) {
                    $q->orWhereIn('city_code', $matchingCityCodes);
                }
            });
        }

        // Generic Catalog Keyword Search (Matches Name, Bio, or Address)
        if ($searchKeyword) {
            $query->where(function ($q) use ($searchKeyword) {
                $q->where('nama', 'LIKE', '%' . $searchKeyword . '%')
                  ->orWhere('deskripsi_bio', 'LIKE', '%' . $searchKeyword . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $searchKeyword . '%');
            });
        }

        // Filter by Province
        if ($selectedProvince) {
            $query->where('province_code', $selectedProvince);
        }

        // Filter by City
        if ($selectedCity) {
            $query->where('city_code', $selectedCity);
        }

        // Filter by Category
        if ($selectedCategory) {
            $query->whereHas('categories', function ($q) use ($selectedCategory) {
                if (is_numeric($selectedCategory)) {
                    $q->where('categories.id', $selectedCategory);
                } else {
                    $q->where('categories.slug', $selectedCategory)
                      ->orWhere('categories.nama_kategori', 'LIKE', '%' . $selectedCategory . '%');
                }
            });
        }

        // Filter by Price Range
        if ($priceMin || $priceMax) {
            $query->whereHas('services', function ($q) use ($priceMin, $priceMax) {
                if ($priceMin) {
                    $q->where('tarif_harga', '>=', $priceMin);
                }
                if ($priceMax) {
                    $q->where('tarif_harga', '<=', $priceMax);
                }
            });
        }

        // Filter by Minimum Rating
        if ($ratingMin) {
            $query->having('testimonials_avg_rating', '>=', (float) $ratingMin);
        }

        // Apply Sorting Clauses
        switch ($selectedSort) {
            case 'rating':
                $query->orderByRaw('testimonials_avg_rating IS NULL, testimonials_avg_rating DESC')
                      ->orderBy('testimonials_count', 'desc');
                break;
            case 'harga_terendah':
                $query->orderByRaw('services_min_tarif_harga IS NULL, services_min_tarif_harga ASC');
                break;
            case 'harga_tertinggi':
                $query->orderByRaw('services_min_tarif_harga IS NULL, services_min_tarif_harga DESC');
                break;
            case 'terpopuler':
                $query->orderBy('contracts_count', 'desc')
                      ->orderBy('testimonials_count', 'desc');
                break;
            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rekomendasi':
            default:
                $query->orderBy('is_verified', 'desc')
                      ->orderByRaw('testimonials_avg_rating IS NULL, testimonials_avg_rating DESC')
                      ->orderBy('created_at', 'desc');
                break;
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $photographers = $query->paginate($perPage)->withQueryString();

        $favoritedIds = [];
        if (Auth::check()) {
            $clientProfile = RoleClient::where('id_user', Auth::id())->first();
            if ($clientProfile) {
                $favoritedIds = $clientProfile->favorites()->pluck('id_photographer')->toArray();
            }
        }

        return view('public.photographers.katalog', compact(
            'photographers',
            'categories',
            'provinces',
            'cities',
            'selectedProvince',
            'selectedCity',
            'selectedCategory',
            'searchKeyword',
            'locationKeyword',
            'priceMin',
            'priceMax',
            'ratingMin',
            'selectedSort',
            'sortLabels',
            'favoritedIds'
        ));
    }

    /**
     * Display Detail Page of a Photographer.
     */
    public function show($id)
    {
        $photographer = RolePhotographer::with([
            'user',
            'province',
            'city',
            'categories',
            'services' => function ($q) {
                $q->whereNotNull('tarif_harga')
                  ->where('tarif_harga', '>', 0)
                  ->whereNotNull('deskripsi_layanan')
                  ->where('deskripsi_layanan', '!=', '')
                  ->with('details');
            },
            'portfolios.medias',
            'availabilities',
            'faqs' => function ($q) {
                $q->where('is_active', true)->orderBy('urutan', 'asc');
            },
            'testimonials.client.user'
        ])->findOrFail($id);

        if (!$photographer->is_onboarded && (!Auth::check() || (Auth::id() !== $photographer->id_user && !Auth::user()->hasRole(['admin', 'super_admin'])))) {
            abort(404, 'Profil fotografer belum aktif atau sedang dalam tahap onboarding.');
        }

        $testimonials = $photographer->testimonials()->with('client.user')->orderBy('created_at', 'desc')->get();
        $totalReviews = $testimonials->count();
        $avgRating = $totalReviews > 0 ? number_format($testimonials->avg('rating'), 1) : '5.0';

        $starCounts = [
            5 => $testimonials->where('rating', 5)->count(),
            4 => $testimonials->where('rating', 4)->count(),
            3 => $testimonials->where('rating', 3)->count(),
            2 => $testimonials->where('rating', 2)->count(),
            1 => $testimonials->where('rating', 1)->count(),
        ];

        // Real-Time Calendar Availability Data (Blocked & Booked Dates)
        $blockedDates = $photographer->availabilities
            ->where('tanggal', '>=', date('Y-m-d'))
            ->mapWithKeys(function ($item) {
                $timeInfo = ($item->jam_mulai && $item->jam_selesai && ($item->jam_mulai != '00:00:00' || $item->jam_selesai != '23:59:59'))
                    ? substr($item->jam_mulai, 0, 5) . ' - ' . substr($item->jam_selesai, 0, 5) . ' WIB'
                    : 'Seharian Penuh';
                return [$item->tanggal => [
                    'keterangan' => $item->keterangan ?: 'Libur / Tidak Menerima Sesi',
                    'time_info' => $timeInfo,
                    'is_all_day' => (!$item->jam_mulai || !$item->jam_selesai || ($item->jam_mulai == '00:00:00' && $item->jam_selesai == '23:59:59')),
                ]];
            })
            ->toArray();

        $contractBookings = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($photographer) {
            $q->where('id_photographer', $photographer->id)
              ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);
        })->where('booking_date', '>=', date('Y-m-d'))
          ->with(['service'])
          ->get();

        $bookedDates = [];
        foreach ($contractBookings as $b) {
            $d = $b->booking_date;
            if (!isset($bookedDates[$d])) {
                $bookedDates[$d] = [];
            }
            $serviceName = $b->service->nama_layanan ?? 'Sesi Foto Terjadwal';
            $jamMulai = substr($b->jam_mulai ?: '08:00', 0, 5);
            $jamSelesai = substr($b->jam_selesai ?: '12:00', 0, 5);
            $durasi = $b->durasi ?: '4 Jam';
            
            $bookedDates[$d][] = [
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'durasi' => $durasi,
                'service_name' => $serviceName,
                'time_formatted' => "{$jamMulai} — {$jamSelesai} WIB",
            ];
        }

        $isFavorited = false;
        if (Auth::check()) {
            $clientProfile = RoleClient::where('id_user', Auth::id())->first();
            if ($clientProfile) {
                $isFavorited = $clientProfile->favorites()->where('id_photographer', $photographer->id)->exists();
            }
        }

        // Sistem FAQ Cerdas — Kumpulan Pertanyaan Umum Pencari Jasa (Universal untuk Seluruh Fotografer)
        $faqs = collect([
            [
                'id' => 1,
                'pertanyaan' => 'Bagaimana alur pemesanan (booking) sesi foto di LensMatch?',
                'jawaban' => "Pilih paket layanan yang sesuai di profil fotografer ini, pilih tanggal yang masih terbuka pada kalender ketersediaan, lalu kirimkan rincian reservasi. Fotografer akan mengonfirmasi jadwal pemotretan Anda maksimal 1x24 jam."
            ],
            [
                'id' => 2,
                'pertanyaan' => 'Bagaimana sistem pembayaran dan jaminan keamanan transaksi saya?',
                'jawaban' => "Pembayaran dilakukan secara resmi melalui transfer bank yang diverifikasi otomatis oleh sistem. Dana pembayaran Anda diamankan oleh sistem platform LensMatch dan baru akan diteruskan ke fotografer setelah sesi pemotretan selesai dan album hasil foto Anda terima."
            ],
            [
                'id' => 3,
                'pertanyaan' => 'Kapan jadwal sesi foto saya dinyatakan resmi terkonfirmasi?',
                'jawaban' => "Jadwal pemotretan resmi terkonfirmasi setelah fotografer menyetujui tanggal reservasi Anda dan pembayaran uang muka (DP 30%) atau pembayaran penuh berhasil terverifikasi oleh sistem."
            ],
            [
                'id' => 4,
                'pertanyaan' => 'Apakah saya bisa berdiskusi mengenai konsep atau tema pemotretan sebelum booking?',
                'jawaban' => "Tentu saja. Anda dapat menekan tombol 'Tanya & Chat Fotografer' pada profil ini untuk mendiskusikan konsep visual, referensi moodboard, busana/wardrobe, maupun rekomendasi spot foto secara langsung dengan mitra fotografer sebelum melakukan booking."
            ],
            [
                'id' => 5,
                'pertanyaan' => 'Bagaimana jika saya perlu mengajukan jadwal ulang (reschedule) sesi foto?',
                'jawaban' => "Anda dapat mengajukan permohonan reschedule kapan saja melalui menu 'Reservasi Saya' dengan memilih tanggal baru yang masih tersedia pada kalender fotografer. Jadwal baru akan berlaku setelah disepakati bersama oleh kedua belah pihak."
            ],
            [
                'id' => 6,
                'pertanyaan' => 'Berapa lama proses penyuntingan (retouching) dan bagaimana saya menerima hasil foto?',
                'jawaban' => "Proses retouching umumnya memerlukan waktu 3 hingga 7 hari kerja tergantung paket layanan yang Anda pilih. Setelah selesai, fotografer akan menyerahkan tautan album Google Drive resolusi tinggi yang dapat Anda akses dan unduh langsung pada menu 'Galeri Hasil Foto Saya'."
            ],
            [
                'id' => 7,
                'pertanyaan' => 'Bagaimana jika terjadi kendala cuaca buruk pada hari pemotretan outdoor?',
                'jawaban' => "Jika terjadi hujan lebat atau cuaca ekstrem pada lokasi outdoor, Anda dan fotografer dapat menyepakati penyesuaian jam pemotretan di hari yang sama atau penjadwalan ulang sesi (reschedule) tanpa biaya tambahan demi hasil karya terbaik."
            ],
        ]);

        return view('public.photographers.show', compact(
            'photographer', 
            'testimonials', 
            'totalReviews', 
            'avgRating', 
            'starCounts',
            'blockedDates',
            'bookedDates',
            'isFavorited',
            'faqs'
        ));
    }

    /**
     * AJAX Endpoint: Get Cities by Province Code.
     */
    public function getCities($provinceCode)
    {
        $cities = City::where('province_code', $provinceCode)
            ->orderBy('name', 'asc')
            ->get(['code', 'name']);

        return response()->json($cities);
    }
}