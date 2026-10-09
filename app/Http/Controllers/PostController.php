<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Posts/Index', [
            'posts' => Post::with(['category', 'user'])->latest()->paginate(5)
        ]);
    }

    public function create()
    {
        return Inertia::render('Posts/Create', [
            'categories' => Category::all()
        ]);
    }

public function show(Post $post)
{
    $post->increment('views');

    $relatedPosts = Post::where('category_id', $post->category_id)
        ->where('id', '!=', $post->id)
        ->with('category')
        ->latest()
        ->take(3)
        ->get();

    $isBookmarked = auth()->check()
        ? auth()->user()->bookmarkedPosts()->where('post_id', $post->id)->exists()
        : false;

    // ตรวจสอบสถานะการกดถูกใจ
    $ip = request()->ip();
    $userId = auth()->id();
    $isLiked = $post->likes()
        ->when($userId, fn ($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn ($q) => $q->whereNull('user_id')->where('ip_address', $ip))
        ->exists();

    return Inertia::render('Posts/Show', [
        'post' => $post->load([
            'category',
            'user',
            'comments.user',
            'comments.replies.user',
        ]),
        'relatedPosts' => $relatedPosts,
        'isBookmarked' => $isBookmarked,
        'isLiked' => $isLiked,
        'likesCount' => $post->likes()->count(),
    ]);
}

    public function edit(Post $post)
    {
        // ป้องกันการแอบเข้า URL: ตรวจสอบสิทธิ์ว่าใช่เจ้าของโพสต์ไหม
        $this->authorize('update', $post);

        return Inertia::render('Posts/Edit', [
            'post' => $post,
            'categories' => Category::all()
        ]);
    }

public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $validated['user_id'] = $request->user()->id;
        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'เผยแพร่บทความใหม่เรียบร้อยแล้ว!');
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'อัปเดตบทความสำเร็จ!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'ลบบทความเรียบร้อยแล้ว');
    }
}