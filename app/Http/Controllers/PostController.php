<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return view('dashboard', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('post.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:3',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'nullable',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $userId = auth()->id();
            $imageName = $userId . "-" . strtotime("now") . "." . $request->image->extension();
            $path = "img/posts";
            $request->image->storeAs($path, $imageName, 'public');

            $validated['image'] = "$path/$imageName";
        } else {
            $validated['image'] = "img/no-image.png";
        }

        Post::create($validated);

        return to_route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('post.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:3',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'nullable',
        ]);

        $post->update($validated);

        return to_route('dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return to_route('dashboard');
    }

    public function generateSlug(Request $request)
    {
        if (!$request->title) {
            return response()->json(['error' => 'Title is required'], 400);
        }

        $slug = SlugService::createSlug(Post::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
