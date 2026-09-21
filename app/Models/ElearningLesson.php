<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElearningLesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elearning_lessons';

    protected $fillable = [
        'id_elearning',
        'id_module',
        'judul',
        'tipe', // video, reading, audio, foto
        'konten',
        'media_type', // youtube, external, upload
        'media_url',
        'media_path',
        'durasi_menit',
        'urutan',
        'is_free',
    ];

    protected $casts = [
        'durasi_menit' => 'integer',
        'urutan' => 'integer',
        'is_free' => 'boolean',
    ];

    /**
     * Relasi ke Kursus E-Learning induk.
     */
    public function elearning()
    {
        return $this->belongsTo(Elearning::class, 'id_elearning');
    }

    /**
     * Relasi ke Modul/Topik.
     */
    public function module()
    {
        return $this->belongsTo(ElearningModule::class, 'id_module');
    }

    /**
     * Relasi ke progress penyelesaian tahapan ini.
     */
    public function progresses()
    {
        return $this->hasMany(ElearningLessonProgress::class, 'id_lesson');
    }

    /**
     * Cek apakah user telah menyelesaikan tahapan ini.
     */
    public function isCompletedByUser(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->progresses()->where('id_user', $userId)->where('is_completed', true)->exists();
    }

    /**
     * Helper URL media yang dapat diputar/dilihat.
     */
    public function getResolvedMediaUrlAttribute(): ?string
    {
        if ($this->media_path) {
            return asset('storage/' . $this->media_path);
        }
        return $this->media_url;
    }

    /**
     * Helper YouTube Embed ID jika tipe video dari YouTube.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->media_url) return null;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $this->media_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        return null;
    }

    /**
     * Lampiran / file pendukung untuk lesson ini.
     * Mengembalikan list lampiran relevan sesuai kursus & materi, atau list kosong jika tidak ada lampiran.
     */
    public function getLampiranListAttribute(): array
    {
        $courseSlug = $this->elearning?->slug ?? '';
        $lessonUrutan = $this->urutan;
        $lessonTipe = $this->tipe;

        // Kursus: Teknik Lighting Portrait Outdoor & Golden Hour
        if ($courseSlug === 'teknik-lighting-portrait-outdoor-golden-hour' && ($lessonUrutan === 2 || $lessonTipe === 'reading')) {
            return [
                [
                    'name' => 'Cheat-Sheet-Teknik-Lighting.pdf',
                    'type' => 'PDF Document',
                    'size' => '2.4 MB',
                    'action_message' => 'Mengunduh file Cheat-Sheet-Teknik-Lighting.pdf...',
                ],
                [
                    'name' => 'Golden-Hour-Preset-Pack.xmp',
                    'type' => 'Preset File',
                    'size' => '850 KB',
                    'action_message' => 'Mengunduh Preset Golden-Hour-Preset-Pack.xmp...',
                ],
            ];
        }

        // Kursus: Mastering Color Grading Lightroom untuk Prewedding
        if ($courseSlug === 'mastering-color-grading-lightroom-untuk-prewedding' && ($lessonUrutan === 2 || $lessonTipe === 'reading')) {
            return [
                [
                    'name' => 'Preset-Prewedding-Warm-Cinematic.xmp',
                    'type' => 'Preset File',
                    'size' => '1.2 MB',
                    'action_message' => 'Mengunduh Preset Lightroom Prewedding...',
                ],
                [
                    'name' => 'Color-Wheel-Grading-Guide.pdf',
                    'type' => 'PDF Document',
                    'size' => '3.1 MB',
                    'action_message' => 'Mengunduh Color-Wheel-Grading-Guide.pdf...',
                ],
            ];
        }

        // Kursus: Strategi Manajemen Bisnis Studio & Escrow Marketplace
        if ($courseSlug === 'strategi-manajemen-bisnis-studio-escrow-marketplace') {
            if ($lessonUrutan === 2 || $lessonTipe === 'reading') {
                return [
                    [
                        'name' => 'Template-Kalkulator-HPP-Studio.xlsx',
                        'type' => 'Excel Spreadsheet',
                        'size' => '640 KB',
                        'action_message' => 'Mengunduh Template Kalkulator HPP Studio...',
                    ],
                ];
            }
            if ($lessonUrutan === 4 || $lessonTipe === 'foto') {
                return [
                    [
                        'name' => 'Draft-Perjanjian-Kerjasama-Fotografer.pdf',
                        'type' => 'PDF Document',
                        'size' => '1.8 MB',
                        'action_message' => 'Mengunduh Draft Kontrak Kerjasama...',
                    ],
                ];
            }
        }

        // Kursus: Panduan Komposisi Foto Arsitektur & Interior Studio
        if ($courseSlug === 'panduan-komposisi-foto-arsitektur-interior-studio' && ($lessonUrutan === 2 || $lessonTipe === 'reading')) {
            return [
                [
                    'name' => 'Checklist-Peralatan-Staging-Interior.pdf',
                    'type' => 'PDF Document',
                    'size' => '1.5 MB',
                    'action_message' => 'Mengunduh Checklist Peralatan Staging Interior...',
                ],
            ];
        }

        // Kursus: Teknik Foto Event Malam Hari & Concert Stage
        if ($courseSlug === 'teknik-foto-event-malam-hari-concert-stage' && ($lessonUrutan === 2 || $lessonTipe === 'reading')) {
            return [
                [
                    'name' => 'Panduan-ISO-Shutter-Speed-Panggung.pdf',
                    'type' => 'PDF Document',
                    'size' => '2.0 MB',
                    'action_message' => 'Mengunduh Panduan ISO & Shutter Speed Panggung...',
                ],
            ];
        }

        // Kursus: Strategi Promosi Portofolio & Negosiasi Klien
        if ($courseSlug === 'strategi-promosi-portofolio-negosiasi-klien' && ($lessonUrutan === 2 || $lessonTipe === 'reading')) {
            return [
                [
                    'name' => 'Template-Proposal-Penawaran-Klien.pdf',
                    'type' => 'PDF Document',
                    'size' => '2.8 MB',
                    'action_message' => 'Mengunduh Template Proposal Penawaran Klien...',
                ],
                [
                    'name' => 'Rate-Card-Pricing-Template.xlsx',
                    'type' => 'Excel Spreadsheet',
                    'size' => '520 KB',
                    'action_message' => 'Mengunduh Rate Card Pricing Template...',
                ],
            ];
        }

        return [];
    }
}
