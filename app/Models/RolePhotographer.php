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
        'kategori_penghapusan',
        'alasan_penghapusan',
    ];

    protected $casts = [
        'is_onboarded' => 'boolean',
        'is_verified' => 'boolean',
    ];

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
        $avg = $this->testimonials()->avg('rating');
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
     * Lencana Reputasi Otomatis (Badges of Excellence) berdasarkan performa riil database.
     * Kepatuhan Anti-AI Slop: Teks bersih, tenang, tanpa bulatan status / dot badge.
     */
    public function getBadgesAttribute()
    {
        $badges = [];

        // 1. "Top Rated" (Rating >= 4.8 dari minimal 5 ulasan)
        $reviewCount = $this->testimonials()->count();
        $avgRating = (float) ($this->testimonials()->avg('rating') ?? 0);
        if ($reviewCount >= 5 && $avgRating >= 4.8) {
            $badges[] = [
                'key' => 'top_rated',
                'label' => 'Top Rated',
                'desc' => "Rating {$avgRating} ({$reviewCount} Ulasan)",
                'color' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-amber-200/80 dark:border-amber-900/50',
                'icon' => 'star',
            ];
        }

        // 2. "Jam Terbang Tinggi" (>= 10 sesi sukses selesai)
        $completedSessions = $this->contracts()->where('status_contract', 'completed')->count();
        if ($completedSessions >= 10) {
            $badges[] = [
                'key' => 'high_flight',
                'label' => 'Jam Terbang Tinggi',
                'desc' => "{$completedSessions}+ Sesi Selesai",
                'color' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200/80 dark:border-blue-900/50',
                'icon' => 'camera',
            ];
        }

        // 3. "Respon Cepat" (Aktif dalam 24 jam terakhir)
        if ($this->user && $this->user->last_seen_at) {
            $diffHours = \Carbon\Carbon::parse($this->user->last_seen_at)->diffInHours(now());
            if ($diffHours <= 24) {
                $badges[] = [
                    'key' => 'fast_response',
                    'label' => 'Respon Cepat',
                    'desc' => 'Aktif dalam 24 jam',
                    'color' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-900/50',
                    'icon' => 'bolt',
                ];
            }
        }

        return $badges;
    }

    /**
     * Metrik Editorial Inline Teks (Standar SweetEscape / Upwork)
     * Bebas dari AI-slop: Tanpa kotak / pil / badge warna-warni buatan.
     */
    public function getEditorialMetricsAttribute(): array
    {
        $reviewCount = $this->testimonials()->count();
        $rawAvg = $this->testimonials()->avg('rating');
        $avgRating = $rawAvg !== null ? round((float) $rawAvg, 1) : null;
        $completedSessions = $this->contracts()->where('status_contract', 'completed')->count();

        $isFastResponse = false;
        if ($this->user && $this->user->last_seen_at) {
            $diffHours = \Carbon\Carbon::parse($this->user->last_seen_at)->diffInHours(now());
            $isFastResponse = ($diffHours <= 24);
        }

        $items = [];

        // 1. Rating & Ulasan (Format: 4.9 (12 ulasan) atau 'Mitra Baru')
        if ($avgRating !== null) {
            $ratingText = number_format($avgRating, 1) . ($reviewCount > 0 ? ' (' . $reviewCount . ' ulasan)' : '');
            $items[] = [
                'type' => 'rating',
                'text' => $ratingText,
                'highlight' => true,
            ];
        } else {
            $items[] = [
                'type' => 'rating',
                'text' => 'Mitra Baru',
                'highlight' => false,
            ];
        }

        // 2. Sesi Selesai (Format: 10+ Sesi Selesai)
        if ($completedSessions >= 10) {
            $items[] = [
                'type' => 'sessions',
                'text' => $completedSessions . '+ Sesi Selesai',
                'highlight' => false,
            ];
        } elseif ($completedSessions > 0) {
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
}
