<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_posts';

    protected $fillable = [
        'id_user',
        'judul',
        'deskripsi',
        'kategori',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'id_forum_post');
    }

    public function rootComments()
    {
        return $this->hasMany(ForumComment::class, 'id_forum_post')->whereNull('parent_id');
    }

    public function medias()
    {
        return $this->hasMany(ForumPostMedia::class, 'id_forum_post');
    }
}