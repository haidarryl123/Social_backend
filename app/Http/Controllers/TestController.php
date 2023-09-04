<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function index(){
        $adminId = Auth::guard('admin')->id();
        $posts = Post::query()->orderBy("id","desc")->get();
        $postController = new PostController();
        foreach ($posts as $key => $post){
            $posts[$key] = $postController->getPostData($adminId,$post);
        }
        return view('preview.preview',compact("posts"));
    }
}
