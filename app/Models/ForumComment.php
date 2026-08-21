<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_comments';

    protected $fillable = [
        'id_forum_post',
        'id_user',
        'comment',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_forum_post');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}