<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPostMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_post_medias';

    protected $fillable = [
        'id_forum_post',
        'foto',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_forum_post');
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
}

