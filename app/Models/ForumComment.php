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
        'parent_id',
        'comment',
        'is_hidden',
    ];

    public function parent()
    {
        return $this->belongsTo(ForumComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_forum_post');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function reports()
    {
        return $this->hasMany(ForumCommentReport::class, 'id_comment');
    }
}