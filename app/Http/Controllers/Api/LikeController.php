<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    public function action(Request $request){
        $userId = Auth::id();
        $post_id = $request->post_id;
        Log::error("user $userId action like post $post_id");
        $checkPost = Post::query()->where(['id' => $post_id])->exists();
        if (!$checkPost){
            return response()->json(["success" => false, "data" => null, "message" => "Post not found."]);
        }

        DB::beginTransaction();
        try {
            $condition = ['user_id' => $userId,'post_id' => $post_id];
            $isLike = Like::query()->where($condition)->exists();
            if ($isLike){
                Like::query()->where($condition)->delete();
                $message = "Unliked!";
            } else {
                Like::query()->create(['user_id' => $userId,'post_id' => $post_id]);
                $message = "Liked!";
            }

            $post = Post::query()->find($post_id);
            $user_id = $post->user_id;
            $post->user = User::query()->where('id',$user_id)->first();
            $comments = Comment::query()->where(['post_id' => $post_id])->get();
            foreach ($comments as $comment){
                $comment->user_comment_info = User::query()->where(['id' => $comment->user_id])->first();
            }
            //$likes = Like::where(['post_id' => $id])->get();
            $post->comments = $comments;
            $post->comment_count = count($comments);

            //$post->likes = $likes;
            $post->like_count = Post::countLike($post_id);

            $post->self_like = Post::isSelfLike($userId,$post_id);
            $post->my_self = Auth::user();

            $post->last_comment = Comment::query()->where(["post_id" => $post->id])->orderBy("created_at","desc")->first();
            if (isset($post->last_comment)){
                $userLastCommentId = $post->last_comment->user_id;
                $post->last_comment->user_comment_last = User::query()->where(['id' => $userLastCommentId])->first();
            }

            DB::commit();
            return response()->json(["success" => true, "data" => $post, "message" => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }
}
