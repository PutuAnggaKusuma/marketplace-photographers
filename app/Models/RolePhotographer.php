<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class RolePhotographer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'role_photographers';

    protected $fillable = [
        'id_user',
        'nama',
        'nomor_telepon',
        'link_sosmed',
        'foto',
        'foto_sampul',
        'alamat',
        'province_code',
        'city_code',
        'deskripsi_bio',
        'nama_bank',
        'nomor_rekening',
        'atas_nama_rekening',
        'is_verified',
        'is_onboarded',
        'status_verifikasi',
        'catatan_penolakan',
        'status_etalase',
        'catatan_penolakan_etalase',
        'jadwal_dikonfirmasi',
        'etalase_diajukan_at',
        'etalase_disetujui_at',
        'kategori_penghapusan',
        'alasan_penghapusan',
    ];

    protected $casts = [
        'is_onboarded' => 'boolean',
        'is_verified' => 'boolean',
        'jadwal_dikonfirmasi' => 'boolean',
        'etalase_diajukan_at' => 'datetime',
        'etalase_disetujui_at' => 'datetime',
    ];

    protected ?array $cachedClientResponseStats = null;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_code', 'code');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'photographer_categories', 'id_photographer', 'id_category');
    }

    public function services()
    {
        return $this->hasMany(PhotographerService::class, 'id_photographer');
    }

    public function portfolios()
    {
        return $this->hasMany(PhotographerPortfolio::class, 'id_photographer');
    }

    /**
     * Hitung jumlah portofolio aktif per kategori milik fotografer.
     * Hanya menyertakan kategori global yang masih aktif (belum dihapus).
     *
     * @return array<int, int>
     */
    public function getUsedPortfolioCategoryCounts(): array
    {
        return $this->portfolios()
            ->join('categories', 'categories.id', '=', 'photographer_portofolios.id_kategori')
            ->whereNull('photographer_portofolios.deleted_at')
            ->whereNull('categories.deleted_at')
            ->selectRaw('photographer_portofolios.id_kategori as cat_id, COUNT(*) as total')
            ->groupBy('photographer_portofolios.id_kategori')
            ->pluck('total', 'cat_id')
            ->mapWithKeys(fn($count, $catId) => [(int) $catId => (int) $count])
            ->toArray();
    }

    public function availabilities()
    {
        return $this->hasMany(PhotographerAvailability::class, 'id_photographer');
    }

    public function workingHours()
    {
        return $this->hasMany(PhotographerWorkingHour::class, 'id_photographer')->orderBy('day_of_week', 'asc');
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class, 'id_photographer');
    }

    public function visibleTestimonials()
    {
        return $this->hasMany(Testimonial::class, 'id_photographer')->where('is_hidden', false);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'id_photographer');
    }

    public function favorites()
    {
        return $this->hasMany(ClientFavorite::class, 'id_photographer');
    }

    public function favoritedByClients()
    {
        return $this->belongsToMany(RoleClient::class, 'client_photographer_favorites', 'id_photographer', 'id_client');
    }

    public function isFavoritedBy($clientId)
    {
        if (!$clientId) return false;
        return $this->favorites()->where('id_client', $clientId)->exists();
    }

    public function getRatingAverageAttribute()
    {
        $avg = $this->visibleTestimonials()->avg('rating');
        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }
        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }
        return asset('storage/' . $this->foto);
    }

    /**
     * Hitung statistik kecepatan respon fotografer terhadap klien (Opsi 2):
     * 1. Kecepatan balas chat klien pada `chat_messages` (rata-rata <= 24 jam, minimal 1 balasan nyata,
     *    mengabaikan sapaan sistem otomatis sebelum klien mengirim pesan, tanpa chat klien terbengkalai > 24 jam).
     * 2. Kedisiplinan merespon order booking `pending` pada `contracts` (tanpa order pending > 24 jam
     *    yang belum dikonfirmasi/ditolak fotografer).
     */
    public function getClientResponseStats(): array
    {
        if ($this->cachedClientResponseStats !== null) {
            return $this->cachedClientResponseStats;
        }

        $now = now();
        $photographerUserId = (int) $this->id_user;
        $threadIds = ChatBooking::where('id_photographer', $this->id)->pluck('id');

        $replyDurationsSeconds = [];
        $overdueChatCount = 0;

        if ($threadIds->isNotEmpty() && $photographerUserId > 0) {
            $messagesByThread = ChatMessage::whereIn('id_chat_booking', $threadIds)
                ->where('deleted_for_all', false)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get(['id', 'id_chat_booking', 'id_sender', 'created_at'])
                ->groupBy('id_chat_booking');

            foreach ($messagesByThread as $threadMessages) {
                $waitingSince = null;

                foreach ($threadMessages as $msg) {
                    if (!$msg->created_at) {
                        continue;
                    }

                    if ((int) $msg->id_sender !== $photographerUserId) {
                        if ($waitingSince === null) {
                            $waitingSince = $msg->created_at;
                        }
                    } else {
                        if ($waitingSince !== null) {
                            $replyDurationsSeconds[] = max(0, $waitingSince->diffInSeconds($msg->created_at));
                            $waitingSince = null;
                        }
                    }
                }

                if ($waitingSince !== null && $waitingSince->diffInSeconds($now) > 86400) {
                    $overdueChatCount++;
                }
            }
        }

        $respondedChatCount = count($replyDurationsSeconds);
        $avgSeconds = null;
        $avgHours = null;
        $avgReplyLabel = null;

        if ($respondedChatCount > 0) {
            $avgSeconds = (int) round(array_sum($replyDurationsSeconds) / $respondedChatCount);
            $avgHours = round($avgSeconds / 3600, 1);

            if ($avgSeconds < 60) {
                $avgReplyLabel = '< 1 menit';
            } elseif ($avgSeconds < 3600) {
                $avgReplyLabel = (int) max(1, round($avgSeconds / 60)) . ' menit';
            } elseif ($avgSeconds < 86400) {
                $formattedHours = rtrim(rtrim(number_format($avgSeconds / 3600, 1, ',', ''), '0'), ',');
                $avgReplyLabel = $formattedHours . ' jam';
            } else {
                $avgReplyLabel = (int) max(1, round($avgSeconds / 86400)) . ' hari';
            }
        }

        $overdueBookingCount = $this->contracts()
            ->whereNotIn('status_contract', ['cancelled', 'completed'])
            ->where('is_validated_photographer', false)
            ->where('created_at', '<', $now->copy()->subHours(24))
            ->whereHas('bookingDetail', function ($q) {
                $q->where('status_booking', 'pending');
            })
            ->count();

        $isFastResponse = ($respondedChatCount >= 1)
            && ($avgSeconds !== null && $avgSeconds <= 86400)
            && ($overdueChatCount === 0)
            && ($overdueBookingCount === 0);

        return $this->cachedClientResponseStats = [
            'responded_chat_count' => $respondedChatCount,
            'avg_reply_seconds' => $avgSeconds,
            'avg_reply_hours' => $avgHours,
            'avg_reply_label' => $avgReplyLabel,
            'overdue_chat_count' => $overdueChatCount,
            'overdue_booking_count' => $overdueBookingCount,
            'is_fast_response' => $isFastResponse,
        ];
    }

    /**
     * Lencana Reputasi Otomatis (Badges of Excellence) berdasarkan performa riil database.
     * Kepatuhan Anti-AI Slop: Teks bersih, tenang, tanpa bulatan status / dot badge.
     */
    public function getBadgesAttribute()
    {
        $badges = [];

        // 1. "Top Rated" (Rating >= 4.8 dari minimal 5 ulasan klien aktif)
        $reviewCount = $this->visibleTestimonials()->count();
        $avgRating = (float) ($this->visibleTestimonials()->avg('rating') ?? 0);
        if ($reviewCount >= 5 && $avgRating >= 4.8) {
            $badges[] = [
                'key' => 'top_rated',
                'label' => 'Top Rated',
                'desc' => "Rating {$avgRating} ({$reviewCount} Ulasan)",
                'color' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-amber-200/80 dark:border-amber-900/50',
                'icon' => 'star',
            ];
        }

        // 2. "Jam Terbang Tinggi" (>= 10 sesi sukses selesai & tervalidasi klien)
        $completedSessions = $this->contracts()
            ->where('status_contract', 'completed')
            ->where('is_validated_client', true)
            ->count();
        if ($completedSessions >= 10) {
            $badges[] = [
                'key' => 'high_flight',
                'label' => 'Jam Terbang Tinggi',
                'desc' => "{$completedSessions}+ Sesi Selesai",
                'color' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200/80 dark:border-blue-900/50',
                'icon' => 'camera',
            ];
        }

        // 3. "Respon Cepat" (Rata-rata balas chat klien <= 24 jam & tanpa order pending menunggak > 24 jam)
        $responseStats = $this->getClientResponseStats();
        if ($responseStats['is_fast_response']) {
            $badges[] = [
                'key' => 'fast_response',
                'label' => 'Respon Cepat',
                'desc' => 'Rata-rata balas ' . $responseStats['avg_reply_label'],
                'color' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-900/50',
                'icon' => 'bolt',
            ];
        }

        return $badges;
    }

    /**
     * Metrik Editorial Inline Teks (Standar SweetEscape / Upwork)
     * Bebas dari AI-slop: Tanpa kotak / pil / badge warna-warni buatan.
     */
    public function getEditorialMetricsAttribute(): array
    {
        $reviewCount = $this->visibleTestimonials()->count();
        $rawAvg = $this->visibleTestimonials()->avg('rating');
        $avgRating = $rawAvg !== null ? round((float) $rawAvg, 1) : 0.0;
        $completedSessions = $this->contracts()
            ->where('status_contract', 'completed')
            ->where('is_validated_client', true)
            ->count();

        $responseStats = $this->getClientResponseStats();
        $isFastResponse = $responseStats['is_fast_response'];

        $items = [];

        // 1. Rating & Ulasan (Format seragam numerik: misal 4.9 (12 ulasan) atau 0.0 (0 ulasan))
        $items[] = [
            'type' => 'rating',
            'text' => number_format($avgRating, 1) . ' (' . $reviewCount . ' ulasan)',
            'highlight' => $reviewCount > 0,
        ];

        // 2. Sesi Selesai (Format: 10+ Sesi Selesai / X Sesi Selesai / 0 Sesi Selesai)
        if ($completedSessions >= 10) {
            $items[] = [
                'type' => 'sessions',
                'text' => $completedSessions . '+ Sesi Selesai',
                'highlight' => false,
            ];
        } else {
            $items[] = [
                'type' => 'sessions',
                'text' => $completedSessions . ' Sesi Selesai',
                'highlight' => false,
            ];
        }

        // 3. Respon Cepat (Format: Respon Cepat)
        if ($isFastResponse) {
            $items[] = [
                'type' => 'fast_response',
                'text' => 'Respon Cepat',
                'highlight' => false,
            ];
        }

        return $items;
    }

    public function faqs()
    {
        return $this->hasMany(PhotographerFaq::class, 'id_photographer')->orderBy('urutan', 'asc')->orderBy('id', 'asc');
    }

    public function clientReviews()
    {
        return $this->hasMany(PhotographerClientReview::class, 'id_photographer');
    }

    /**
     * Hitung field profil fotografer yang belum lengkap diisi.
     */
    public function getMissingProfileFields(): array
    {
        $missing = [];
        
        $nama = trim($this->nama ?: ($this->user?->nama ?: ''));
        if (empty($nama)) {
            $missing['nama'] = 'Nama Lengkap';
        }

        $email = trim($this->user?->email ?: '');
        if (empty($email)) {
            $missing['email'] = 'Email';
        }

        $nomorTelepon = trim($this->nomor_telepon ?: '');
        if (empty($nomorTelepon)) {
            $missing['nomor_telepon'] = 'Nomor WhatsApp';
        }

        $linkSosmed = trim($this->link_sosmed ?: '');
        if (empty($linkSosmed)) {
            $missing['link_sosmed'] = 'Instagram';
        }

        $prov = trim($this->province_code ?: '');
        if (empty($prov)) {
            $missing['province_code'] = 'Provinsi';
        }

        $city = trim($this->city_code ?: '');
        if (empty($city)) {
            $missing['city_code'] = 'Kota / Kabupaten';
        }

        $alamat = trim($this->alamat ?: '');
        if (empty($alamat)) {
            $missing['alamat'] = 'Alamat';
        }

        if ($this->categories()->count() === 0) {
            $missing['categories'] = 'Kategori';
        }

        $bio = trim($this->deskripsi_bio ?: '');
        if (empty($bio) || strlen($bio) < 15) {
            $missing['deskripsi_bio'] = 'Bio';
        }

        $foto = trim($this->foto ?: '');
        if (empty($foto)) {
            $missing['foto'] = 'Foto Profil';
        }

        $fotoSampul = trim($this->foto_sampul ?: '');
        if (empty($fotoSampul)) {
            $missing['foto_sampul'] = 'Foto Sampul';
        }

        $namaBank = trim($this->nama_bank ?: '');
        if (empty($namaBank)) {
            $missing['nama_bank'] = 'Nama Bank';
        }

        $nomorRekening = trim($this->nomor_rekening ?: '');
        if (empty($nomorRekening)) {
            $missing['nomor_rekening'] = 'Nomor Rekening';
        }

        $atasNama = trim($this->atas_nama_rekening ?: '');
        if (empty($atasNama)) {
            $missing['atas_nama_rekening'] = 'Nama Pemilik Rekening';
        }

        return $missing;
    }

    /**
     * Hitung persentase kelengkapan data profil fotografer (0 - 100%).
     */
    public function getProfileCompletionPercentage(): int
    {
        $totalRequired = 14;
        $missingCount = count($this->getMissingProfileFields());
        $filledCount = max(0, $totalRequired - $missingCount);
        return (int) round(($filledCount / $totalRequired) * 100);
    }

    /**
     * Cek apakah seluruh data profil fotografer sudah terisi 100% lengkap.
     */
    public function isProfileComplete(): bool
    {
        return count($this->getMissingProfileFields()) === 0;
    }

    /**
     * Hitung status kelengkapan 4 seksi utama profil (Gaya 3B: Dot Step Indicator).
     */
    public function getProfileSectionsStatus(): array
    {
        $missing = $this->getMissingProfileFields();

        $sections = [
            'kontak' => [
                'name' => 'Kontak',
                'is_complete' => !isset($missing['nama']) && !isset($missing['email']) && !isset($missing['nomor_telepon']) && !isset($missing['link_sosmed']),
            ],
            'domisili' => [
                'name' => 'Domisili & Kategori',
                'is_complete' => !isset($missing['province_code']) && !isset($missing['city_code']) && !isset($missing['alamat']) && !isset($missing['categories']),
            ],
            'rekening' => [
                'name' => 'Rekening Bank',
                'is_complete' => !isset($missing['nama_bank']) && !isset($missing['nomor_rekening']) && !isset($missing['atas_nama_rekening']),
            ],
            'media' => [
                'name' => 'Foto & Bio',
                'is_complete' => !isset($missing['foto']) && !isset($missing['foto_sampul']) && !isset($missing['deskripsi_bio']),
            ],
        ];

        $completedCount = 0;
        $incompleteNames = [];
        foreach ($sections as $sec) {
            if ($sec['is_complete']) {
                $completedCount++;
            } else {
                $incompleteNames[] = $sec['name'];
            }
        }

        return [
            'sections' => $sections,
            'completed_count' => $completedCount,
            'total_sections' => count($sections),
            'incomplete_names' => $incompleteNames,
        ];
    }

    public function isVerified(): bool
    {
        return $this->status_verifikasi === 'terverifikasi' || (bool) $this->is_verified;
    }

    public function isPending(): bool
    {
        return $this->status_verifikasi === 'menunggu';
    }

    public function isRejected(): bool
    {
        return $this->status_verifikasi === 'ditolak';
    }

    /**
     * Cek apakah fitur operasional fotografer boleh dibuka (Profil 100% Lengkap DAN di-ACC Admin).
     */
    public function isFeatureUnlocked(): bool
    {
        return $this->isProfileComplete() && $this->isVerified();
    }

    public function hasValidServices(): bool
    {
        return $this->services()
            ->whereNotNull('tarif_harga')
            ->where('tarif_harga', '>', 0)
            ->whereNotNull('deskripsi_layanan')
            ->where('deskripsi_layanan', '!=', '')
            ->exists();
    }

    public function hasValidPortfolios(): bool
    {
        return $this->portfolios()
            ->whereHas('medias')
            ->exists();
    }

    public function hasConfiguredSchedule(): bool
    {
        return (bool) $this->jadwal_dikonfirmasi
            && $this->workingHours()->where('is_open', true)->exists();
    }

    public function isStorefrontComplete(): bool
    {
        return $this->hasValidServices()
            && $this->hasValidPortfolios()
            && $this->hasConfiguredSchedule();
    }

    public function getStorefrontChecklist(): array
    {
        $hasServices = $this->hasValidServices();
        $hasPortfolios = $this->hasValidPortfolios();
        $hasSchedule = $this->hasConfiguredSchedule();

        $items = [
            'services' => [
                'label' => 'Paket Layanan',
                'done' => $hasServices,
            ],
            'portfolios' => [
                'label' => 'Portofolio',
                'done' => $hasPortfolios,
            ],
            'schedule' => [
                'label' => 'Jadwal',
                'done' => $hasSchedule,
            ],
        ];

        $missingLabels = [];
        $completedCount = 0;
        foreach ($items as $item) {
            if ($item['done']) {
                $completedCount++;
            } else {
                $missingLabels[] = $item['label'];
            }
        }

        return [
            'items' => $items,
            'completed_count' => $completedCount,
            'total_count' => count($items),
            'missing_labels' => $missingLabels,
            'is_ready' => $completedCount === count($items),
        ];
    }

    public function isStorefrontPending(): bool
    {
        return $this->status_etalase === 'menunggu';
    }

    public function isStorefrontApproved(): bool
    {
        return $this->status_etalase === 'disetujui';
    }

    public function isStorefrontRejected(): bool
    {
        return $this->status_etalase === 'ditolak';
    }

    public function isPublishedToCatalog(): bool
    {
        return $this->isProfileComplete()
            && $this->isVerified()
            && $this->isStorefrontApproved()
            && $this->isStorefrontComplete();
    }

    public function scopePublished($query)
    {
        return $query->where('is_onboarded', true)
            ->where('status_verifikasi', 'terverifikasi')
            ->where('status_etalase', 'disetujui')
            ->where('jadwal_dikonfirmasi', true)
            ->whereHas('workingHours', function ($q) {
                $q->where('is_open', true);
            })
            ->whereHas('services', function ($q) {
                $q->whereNotNull('tarif_harga')
                    ->where('tarif_harga', '>', 0)
                    ->whereNotNull('deskripsi_layanan')
                    ->where('deskripsi_layanan', '!=', '');
            })
            ->whereHas('portfolios', function ($q) {
                $q->whereHas('medias');
            });
    }

    /**
     * Urutkan fotografer published berdasarkan performa bulan berjalan:
     * 1. Sesi completed tervalidasi klien pada bulan berjalan terbanyak
     * 2. Ulasan positif (rating >= 4) pada bulan berjalan terbanyak
     * Fallback otomatis bila bulan berjalan belum ada transaksi selesai:
     * 3. Rata-rata rating tertinggi (testimonials_avg_rating)
     * 4. Jumlah ulasan terbanyak (testimonials_count)
     * 5. Total sesi completed tervalidasi klien keseluruhan terbanyak
     */
    public function scopeFeaturedThisMonth($query)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return $query->published()
            ->withCount([
                'contracts as monthly_completed_sessions_count' => function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('status_contract', 'completed')
                      ->where('is_validated_client', true)
                      ->where(function ($dateQ) use ($startOfMonth, $endOfMonth) {
                          $dateQ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                                ->orWhereBetween('created_at', [$startOfMonth, $endOfMonth]);
                      });
                },
                'testimonials as monthly_positive_reviews_count' => function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('is_hidden', false)
                      ->where('rating', '>=', 4)
                      ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                },
                'testimonials as testimonials_count' => fn($q) => $q->where('is_hidden', false),
                'contracts as completed_sessions_count' => function ($q) {
                    $q->where('status_contract', 'completed')
                      ->where('is_validated_client', true);
                },
            ])
            ->withAvg(['testimonials as testimonials_avg_rating' => fn($q) => $q->where('is_hidden', false)], 'rating')
            ->orderByDesc('monthly_completed_sessions_count')
            ->orderByDesc('monthly_positive_reviews_count')
            ->orderByRaw('COALESCE(testimonials_avg_rating, 0) DESC')
            ->orderByDesc('testimonials_count')
            ->orderByDesc('completed_sessions_count')
            ->orderBy('id', 'asc');
    }

    public function syncStorefrontIntegrity(): bool
    {
        if (in_array($this->status_etalase, ['menunggu', 'disetujui'], true) && !$this->isStorefrontComplete()) {
            $this->update([
                'status_etalase' => 'belum_diajukan',
            ]);
            return true;
        }

        return false;
    }
}
