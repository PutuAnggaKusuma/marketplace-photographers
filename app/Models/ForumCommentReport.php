<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCommentReport extends Model
{
    use HasFactory;

    protected $table = 'forum_comment_reports';

    protected $fillable = [
        'id_comment',
        'id_user',
        'kategori',
        'alasan',
        'status',
    ];

    public function comment()
    {
        return $this->belongsTo(ForumComment::class, 'id_comment');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'spam' => 'Spam / Iklan',
            'harassment' => 'Pelecehan / Kata Kasar',
            'inappropriate' => 'Konten Tidak Pantas',
            'misinformation' => 'Informasi Palsu / Hoaks',
            default => 'Lainnya',
        };
    }
}

