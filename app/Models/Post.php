<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $table = "posts";
    protected $guarded = [];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

    public static function checkAuthorize($userId,$post_id){
        return Post::query()->where(['id' => $post_id,'user_id' => $userId])->exists();
    }

    public static function countComment($post_id){
        return Comment::query()->where(['post_id' => $post_id])->count();
    }

    public static function countLike($post_id){
        return Like::query()->where(['post_id' => $post_id])->count();
    }

    public static function isSelfLike($user_id,$post_id){
        return Like::query()->where(['user_id' => $user_id,'post_id' => $post_id])->exists();
    }
}
