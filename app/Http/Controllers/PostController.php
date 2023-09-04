<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(){
        $adminId = Auth::guard('admin')->id();
        $posts = Post::query()->orderBy("id","desc")->get();
        foreach ($posts as $key => $post){
            $posts[$key] = $this->getPostData($adminId,$post);
        }
        return view("onstagram.main.posts",compact("posts"));
    }

    public function postDetail($postId){
        $adminId = Auth::guard('admin')->id();
        $post = Post::query()->where(["id" => $postId])->first();
        if (!isset($post)){
            return redirect()->route("post_management");
        }
        $post = $this->getPostData($adminId,$post,true);
        return view("onstagram.main.post_detail",compact("post"));
    }

    public function getPostData($adminId,$post,$allComment = false){
        $postId = $post->id;
        $userId = $post->user_id;
        $user = User::query()->where(['id' => $userId])->first();
        $post->user = $user;
        $post->total_like = Like::query()->where(["post_id" => $postId])->count();
        $post->total_comment = Comment::query()->where(["post_id" => $postId])->count();
        $post->self_like = Like::query()->where(["user_id" => $adminId,"post_id" => $postId])->exists();
        $post->myself = Auth::guard('admin')->user();
        if ($allComment){
            $comments = Comment::query()->where(["post_id" => $postId])->get();
            foreach ($comments as $comment){
                $commentUserId = $comment->user_id;
                $comment->user = User::query()->where(['id' => $commentUserId])->first();
            }
            $post->comments = $comments;
        } else {
            $post->last_comment = Comment::query()->where(["post_id" => $postId])->orderBy("created_at","desc")->first();
            if (isset($post->last_comment)){
                $userLastCommentId = $post->last_comment->user_id;
                $post->last_comment->user_comment_last = User::query()->where(['id' => $userLastCommentId])->first();
            }
        }
        return $post;
    }

    public function likePost(Request $request){
        $userId = Auth::guard("admin")->id();
        $post_id = $request->post_id;

        $checkPost = Post::query()->where(['id' => $post_id])->exists();
        if (!$checkPost){
            return response()->json(["result" => "error", "data" => null, "message" => "Post not found."]);
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
            $totalLike = Like::query()->where(["post_id" => $post_id])->count();
            DB::commit();
            return response()->json(["result" => "success", "data" => $totalLike, "message" => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["result" => "error", "data" => null, "message" => $e->getMessage()]);
        }
    }

    public function commentPost(Request $request){
        $userId = Auth::guard("admin")->id();
        $post_id = $request->post_id;
        $comment = $request->comment;

        $checkPost = Post::query()->where(['id' => $post_id])->exists();
        if (!$checkPost){
            return response()->json(["result" => "error", "data" => null, "message" => "Post not found."]);
        }

        DB::beginTransaction();
        try {
            $commentCreated = Comment::query()->create([
                'user_id' => $userId,
                'post_id' => $post_id,
                'comment' => $comment
            ]);
            $commentCreated->user = User::query()->find($userId);
            $totalComment = Comment::query()->where(["post_id" => $post_id])->count();
            DB::commit();
            return response()->json(["result" => "success", "data" => $commentCreated,"totalComment" => $totalComment, "message" => "Comment saved."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["result" => "error", "data" => null, "message" => $e->getMessage()]);
        }
    }

    public function deletePost(Request $request){
        DB::beginTransaction();
        try {
            $post_id = $request->post_id;
            $condition = ['id' => $post_id];
            $post = Post::query()->where($condition)->first();
            $photo = $post->photo;
            if (isset($photo) && strlen(trim($photo)) > 0){
                $photo = str_replace("/storage/images/", "", $photo);
                Storage::delete("public/images/$photo");
            }

            Post::query()->where($condition)->delete();
            Comment::query()->where(["post_id" => $post_id])->delete();
            Like::query()->where(["post_id" => $post_id])->delete();
            DB::commit();
            return response()->json(["result" => "success", "data" => null, "message" => "Post deleted."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["result" => "error", "data" => null, "message" => $e->getMessage()]);
        }
    }

    public function deleteComment(Request $request){
        DB::beginTransaction();
        try {
            $comment_id = $request->comment_id;

            $comment = Comment::query()->where(['id' => $comment_id])->first();
            if (!isset($comment)){
                return response()->json(["result" => "error", "data" => null, "message" => "Comment not found"]);
            }
            $post_id = $comment->post_id;

            Comment::query()->where(['id' => $comment_id])->delete();
            $totalComment = Comment::query()->where(["post_id" => $post_id])->count();
            DB::commit();
            return response()->json(["result" => "success", "data" => $totalComment, "message" => "Comment deleted."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["result" => "error", "data" => null, "message" => $e->getMessage()]);
        }
    }
}
