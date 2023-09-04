<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/auth')->group(function () {
    Route::post('/login','Api\AuthController@login');
    Route::post('/register','Api\AuthController@register');
    Route::post('/logout','Api\AuthController@logout');

    Route::prefix('/profile')->middleware("jwt")->group(function () {
        Route::post('/save','Api\AuthController@saveProfile');
        Route::post('/get-wall','Api\PostController@getWall');
    });
});

Route::prefix('/post')->group(function () {

});
Route::prefix('/post')->middleware('jwt')->group(function () {
    Route::post('/get','Api\PostController@get');
    Route::post('/get-single-post','Api\PostController@getSinglePost');
    Route::post('/create','Api\PostController@create');
    Route::post('/update','Api\PostController@update');
    Route::post('/delete','Api\PostController@delete');
    Route::post('/get-my-post','Api\PostController@getMyPost');
});

Route::prefix('/comment')->middleware('jwt')->group(function () {
    Route::post('/get','Api\CommentController@get');
    Route::post('/create','Api\CommentController@create');
    Route::post('/update','Api\CommentController@update');
    Route::post('/delete','Api\CommentController@delete');
});

Route::prefix('/like')->middleware('jwt')->group(function () {
    Route::post('/','Api\LikeController@action');
});

