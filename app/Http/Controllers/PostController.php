<?php

namespace App\Http\Controllers;

use App\Events\PostPublished;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{

    /**
     * Get a listing of posts.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {

        // ignores pagination

        $data = Post::all();

        return response()->json([
            'status'  => true,
            'message' => 'OK.',
            'data'    => $data,
            'error'   => null,
        ]);
    }

    /**
     * Store post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request, $topicIdentifier): JsonResponse
    {
        $request->merge([
            'identifier' => $topicIdentifier,
        ]);
        $request->validate([
            'title'         => 'required|string|max:250|unique:posts,title',
            'body'          => 'required|string|max:50000',
            'identifier'      => 'required|string|exists:topics,identifier',
        ]);

        $topic = Topic::where('identifier', $request->identifier)->first();

        $post = new Post([
            'title'       => $request->title,
            'body'        => $request->body,
            'topic_id'    => $topic->id,
        ]);
        $post->slug = Str::slug($request->title);
        $post->user_id = auth()->id();
        $post->save();

        // dispatch event
        PostPublished::dispatch($post);

        return response()->json([
            'status'  => true,
            'message' => 'Post created.',
            'data'    => $post,
            'error'   => null,
        ], 201);
    }
}
