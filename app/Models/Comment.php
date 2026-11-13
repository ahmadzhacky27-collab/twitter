<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'comment',
        'image',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi untuk Balasan Komentar (Replies)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with(['user', 'likes']);
    }

    // Relasi untuk Like pada Komentar
    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }
}
