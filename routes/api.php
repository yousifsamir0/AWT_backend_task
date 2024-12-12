<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post("login", [AuthController::class, 'login']);
Route::post("register", [AuthController::class, 'register']);
Route::post("logout", [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::apiResource('tags', TagController::class);

Route::get('posts/soft', [PostController::class, 'getSoftDeleted']);
Route::post('posts/soft/{postId}', [PostController::class, 'restore']);
Route::apiResource('posts', PostController::class);

Route::get('stats', function (Request $request) {
    $usersCount = DB::table('users')->count();
    $postsCount = DB::table('posts')->count();
    $usersWithZeroPosts = User::has('posts', '<', 1)->count();

    return [
        'users#' => $usersCount,
        'posts#' => $postsCount,
        'usersWithZeroPosts#' => $usersWithZeroPosts,
    ];
});
