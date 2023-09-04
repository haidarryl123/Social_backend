<?php

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route("login");
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test', 'TestController@index')->name('test');

Route::prefix('/auth')->group(function () {
    Route::get('/login','AuthController@login')->name("login");
    Route::post('/post-login','AuthController@postLogin')->name("post_login");
    Route::get('/logout','AuthController@logout')->name("logout");
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function(){
    $totalUser = User::query()->count();
    View::share('totalUser', $totalUser);

    $totalPost = Post::query()->count();
    View::share('totalPost', $totalPost);

    $totalLike = Like::query()->count();
    View::share('totalLike', $totalLike);

    $totalComment = Comment::query()->count();
    View::share('totalComment', $totalComment);

    Route::get('/user','UserController@index')->name("user_management");
    Route::post('/user-datatable','UserController@userDatatable')->name("user_datatable");
    Route::post('/user-action','UserController@userAction')->name("user_action");
    Route::get('/user/wall/{userId}','UserController@wall')->name("wall");

    Route::get('/post','PostController@index')->name("post_management");
    Route::get('/post/detail/{postId}','PostController@postDetail')->name("post_detail");
    Route::post('/post/like','PostController@likePost')->name("like_post");
    Route::post('/post/comment','PostController@commentPost')->name("comment_post");
    Route::post('/post/delete','PostController@deletePost')->name("delete_post");
    Route::post('/comment/delete','PostController@deleteComment')->name("delete_comment");
});

Route::get('/pre-view','TestController@index')->name("preview");
