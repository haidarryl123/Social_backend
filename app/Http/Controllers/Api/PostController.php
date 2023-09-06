<?php

namespace App\Http\Controllers\Api;

use App\Common\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected $helper;

    public function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function getMyPost(){
        $userId = Auth::id();
        $user = Auth::user();
        $posts = Post::query()->where(['user_id' => $userId])->orderBy('id','desc')->get();
        $data['user'] = $user;
        $data['post'] = $posts;
        return response()->json(["success" => true, "data" => $data, "message" => "Get data successfully."]);
    }

    public function getWall(Request $request){
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!isset($user)){
            return response()->json(["success" => false, "data" => null, "message" => "User not found"]);
        }
        $posts = Post::query()->where(['user_id' => $userId])->orderBy('id','desc')->get();
        foreach ($posts as $post){
            $id = $post->id;
            //$userPostId = $post->user_id;
            //$post->user_post = User::query()->find($userPostId);
            $post->comment_count = Comment::query()->where(['post_id' => $id])->count();
            $post->like_count = Post::countLike($id);
            $post->self_like = Post::isSelfLike(Auth::id(),$id);
        }
        $data['user'] = $user;
        $data['post'] = $posts;
        return response()->json(["success" => true, "data" => $data, "message" => "Get data successfully."]);
    }

    public function getSinglePost(Request $request){
        $userId = Auth::id();
        $postId = $request->post_id;
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
        return response()->json(["success" => true, "data" => $post, "message" => "Get data successfully."]);
    }

    public function get(){
        $userId = Auth::id();
        $posts = Post::query()->orderBy('id','desc')->get();
        foreach ($posts as $post){
            $id = $post->id;
            $user_id = $post->user_id;
            $post->user = User::query()->where('id',$user_id)->first();
            $comments = Comment::query()->where(['post_id' => $id])->get();
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
        }
        return response()->json(["success" => true, "data" => $posts, "message" => "Get data successfully."]);
    }

    public function create(Request $request){
        $userId = Auth::id();
        $description = $request->description;
        $photo = $request->photo;

        $validate = $this->validatePost($description);
        $result = $validate['success'];
        if (!$result){
            $message = $validate['message'];
            return response()->json(["success" => false, "data" => null, "message" => $message]);
        }

        DB::beginTransaction();
        try {
            $pathSavedImage = null;
            if (isset($photo) && $photo != ''){
//                $uploadFileDirectory = "storage/images/";
//                $image = time().".jpg";
//                file_put_contents($uploadFileDirectory.$image,$request->file('photo'));
//                $imagePath = "/".$uploadFileDirectory.$image;

                $storagePath = Storage::disk('local')->getAdapter()->getPathPrefix();
                $uploadFileDirectory = 'public/images';
                $storagePath = $storagePath . $uploadFileDirectory;
                $this->helper->checkExistDirectory($storagePath);
                if ($request->hasFile('photo')) {
                    $pathSavedImage = $request->file('photo')->store($uploadFileDirectory);
                    $pathSavedImage = str_replace('public','/storage',$pathSavedImage);
                }
            }
            $post = Post::query()->create([
                'user_id' => $userId,
                'description' => $description,
                'photo' => $pathSavedImage
            ]);
            $post->user = Auth::user();
            $post->comments = [];
            $post->comment_count = 0;
            $post->like_count = 0;
            $post->self_like = false;
            $post->my_self = Auth::user();
            $post->last_comment = null;
            DB::commit();
            return response()->json(["success" => true, "data" => $post, "message" => "Post created."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }

    public function update(Request $request){
        $userId = Auth::id();
        $post_id = $request->id;
        $description = $request->description;

        $validate = $this->validatePost($description);
        $result = $validate['success'];
        if (!$result){
            $message = $validate['message'];
            return response()->json(["success" => false, "data" => null, "message" => $message]);
        }

        $exist = Post::checkAuthorize($userId,$post_id);
        if (!$exist) {
            return response()->json(["success" => false, "data" => null, "message" => "Post not found."]);
        }

        DB::beginTransaction();
        try {
            Post::query()->where(['id' => $post_id,'user_id' => $userId])->update([
                'description' => $description
            ]);
            DB::commit();
            return response()->json(["success" => true, "data" => null, "message" => "Post edited."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }

    private function validatePost($description){
        if (!isset($description) || strlen(trim($description)) == 0){
            return ['success' => false,'message' => 'Description is required.'];
        }
        return ['success' => true,'message' => ''];
    }

    public function delete(Request $request){
        $userId = Auth::id();
        $post_id = $request->id;

        $exist = Post::checkAuthorize($userId,$post_id);
        if (!$exist) {
            return response()->json(["success" => false, "data" => null, "message" => "Post not found."]);
        }

        DB::beginTransaction();
        try {
            $condition = ['id' => $post_id,'user_id' => $userId];
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
            return response()->json(["success" => true, "data" => intval($post_id), "message" => "Post deleted."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }
}
