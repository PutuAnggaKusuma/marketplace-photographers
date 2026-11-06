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
        return round($this->testimonials()->avg('rating') ?? 4.9, 1);
    }

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80';
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
        $avgRating = round((float) ($this->testimonials()->avg('rating') ?? 4.9), 1);
        $completedSessions = $this->contracts()->where('status_contract', 'completed')->count();

        $isFastResponse = false;
        if ($this->user && $this->user->last_seen_at) {
            $diffHours = \Carbon\Carbon::parse($this->user->last_seen_at)->diffInHours(now());
            $isFastResponse = ($diffHours <= 24);
        }

        $items = [];

        // 1. Rating & Ulasan (Format: 4.9 (12 ulasan))
        $ratingText = number_format($avgRating, 1) . ($reviewCount > 0 ? ' (' . $reviewCount . ' ulasan)' : '');
        $items[] = [
            'type' => 'rating',
            'text' => $ratingText,
            'highlight' => true,
        ];

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
}
