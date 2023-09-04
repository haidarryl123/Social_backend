<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function get(Request $request){
        $post_id = $request->post_id;
        if (!isset($post_id) || strlen(trim($post_id)) == 0){
            return response()->json(["success" => false, "data" => null, "message" => "Post not found."]);
        }

        $checkPost = Post::query()->where(['id' => $post_id])->exists();
        if (!$checkPost){
            return response()->json(["success" => false, "data" => null, "message" => "Post not found."]);
        }

        $comments = Comment::query()->where(['post_id' => $post_id])->get();
        foreach ($comments as $comment){
            $user_id = $comment->user_id;
            $comment->user = User::query()->find($user_id);
        }
        return response()->json(["success" => true, "data" => $comments, "message" => "Get data successfully."]);
    }

    public function create(Request $request){
        $userId = Auth::id();
        $post_id = $request->post_id;
        $comment = $request->comment;

        $checkPost = Post::query()->where(['id' => $post_id])->exists();
        if (!$checkPost){
            return response()->json(["success" => false, "data" => null, "message" => "Post not found."]);
        }

        $validate = $this->validateComment($comment);
        $result = $validate['success'];
        if (!$result){
            $message = $validate['message'];
            return response()->json(["success" => false, "data" => null, "message" => $message]);
        }

        DB::beginTransaction();
        try {
            $commentCreated = Comment::query()->create([
                'user_id' => $userId,
                'post_id' => $post_id,
                'comment' => $comment
            ]);
            $commentCreated->user = User::query()->find($userId);
            DB::commit();

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

            return response()->json(["success" => true, "data" => $commentCreated,"post" => $post, "message" => "Comment saved."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }

    public function update(Request $request){
        $userId = Auth::id();
        $comment_id = $request->id;
        $comment = $request->comment;
        Log::debug("user $userId update comment id : $comment_id with content: $comment");

        $validate = $this->validateComment($comment);
        $result = $validate['success'];
        if (!$result){
            $message = $validate['message'];
            return response()->json(["success" => false, "data" => null, "message" => $message]);
        }

        $exist = Comment::checkAuthorize($userId,$comment_id);
        if (!$exist) {
            return response()->json(["success" => false, "data" => null, "message" => "Comment not found."]);
        }

        DB::beginTransaction();
        try {
            Comment::query()->where(['id' => $comment_id,'user_id' => $userId])->update([
                'comment' => $comment
            ]);
            $commentUpdated = Comment::query()->where("id",$comment_id)->first();
            $commentUpdated->user = User::query()->find($userId);
            DB::commit();
            return response()->json(["success" => true, "data" => $commentUpdated, "message" => "Comment edited."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }

    private function validateComment($comment){
        if (!isset($comment) || strlen(trim($comment)) == 0){
            return ['success' => false,'message' => 'Comment can not be empty.'];
        }
        return ['success' => true,'message' => ''];
    }

    public function delete(Request $request){
        $userId = Auth::id();
        $comment_id = $request->id;

        $exist = Comment::checkAuthorize($userId,$comment_id);
        if (!$exist) {
            return response()->json(["success" => false, "data" => null, "message" => "Comment not found."]);
        }

        DB::beginTransaction();
        try {
            $comment = Comment::query()->where(['id' => $comment_id,'user_id' => $userId])->first();
            $postId = $comment->post_id;
            Comment::query()->where(['id' => $comment_id,'user_id' => $userId])->delete();

            $post = Post::query()->find($postId);
            if (!isset($post)){
                return response()->json(["success" => false, "data" => null, "message" => "Post not found."]);
            }
            $id = $post->id;
            $user_id = $post->user_id;
            $post->user = User::query()->where('id',$user_id)->first();
            $comments = Comment::query()->where(['post_id' => $id])->get();
            foreach ($comments as $comment){
                $comment->user_comment_info = User::query()->where(['id' => $comment->user_id])->first();
            }

            //$likes = Like::where(['post_id' => $id])->get();
            $post->comments = $comments;
            $post->comment_count = count($comments);

            //$post->likes = $likes;
            $post->like_count = Post::countLike($id);

            $post->self_like = Post::isSelfLike($userId,$id);
            $post->my_self = Auth::user();

            $post->last_comment = Comment::query()->where(["post_id" => $post->id])->orderBy("created_at","desc")->first();
            if (isset($post->last_comment)){
                $userLastCommentId = $post->last_comment->user_id;
                $post->last_comment->user_comment_last = User::query()->where(['id' => $userLastCommentId])->first();
            }

            DB::commit();
            return response()->json(["success" => true, "data" => $post, "message" => "Comment deleted."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }
}
