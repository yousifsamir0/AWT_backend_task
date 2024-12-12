<?php

namespace App\Http\Controllers;

use App\Jobs\FetchRandomUserJob;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum')
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        (new \App\Jobs\FetchRandomUserJob());
        $posts = $request->user()->posts()->orderBy('pinned', 'desc')->get();
        return ['posts' => $posts];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // save file
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pinned' => 'required|boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($request->hasFile('cover_image')) {

            $file = $request->file('cover_image');
            $uniqueName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('cover_images', $uniqueName, 'public');
            $imageUrl = asset('storage/' . $imagePath);
            $validated['cover_image'] = $imageUrl;
        }

        $post = $request->user()->posts()->create($validated);
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return $post;
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        Gate::authorize('view', $post);
        return $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('view', $post);
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required|string',
            'pinned' => 'required|boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }
        $post->update($validated);

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('view', $post);
        $post->delete();
        return [
            'message' => 'post soft deleted successfully !'
        ];
    }

    public function getSoftDeleted(Request $request)
    {
        $uid = $request->user()->id;
        $posts = Post::onlyTrashed()->where('user_id', $uid)->get();
        return ['posts' => $posts];
    }
    public function restore(Request $request, string $postId)
    {

        $post = Post::onlyTrashed()->where('id', $postId)->firstOrFail();
        Gate::authorize('restore', $post);
        $post->restore();
        return ['message' => 'post restored successfully'];
    }
}
