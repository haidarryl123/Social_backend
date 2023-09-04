<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "comments";
    protected $guarded = [];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function posts(){
        return $this->belongsTo(Post::class);
    }

    public static function checkAuthorize($userId,$comment_id){
        return Comment::query()->where(['id' => $comment_id,'user_id' => $userId])->exists();
    }
}
