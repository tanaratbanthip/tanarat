<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
        'title' => 'required|string|max:255',
        'content' => 'required',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:3072',
    ]);

    $data = $validated;
    $data['user_id'] = $request->user()->id;
    $data['slug'] = \Illuminate\Support\Str::slug($request->title) . '-' . uniqid();

    // อัปโหลดขึ้น Cloudinary โฟลเดอร์ posts
if ($request->hasFile('image')) {
    $uploaded = Cloudinary::upload($request->file('image')->getRealPath(), [
        'folder' => 'posts',
    ]);
    $data['image'] = $uploaded->getSecurePath();
}

    $post = Post::create($data);

    return redirect()->route('posts.show', $post->slug)->with('success', 'สร้างบทความเรียบร้อยแล้ว!');
}

 public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:3072',
    ]);

    $data = $validated;

if ($request->hasFile('image')) {
    $uploaded = Cloudinary::upload($request->file('image')->getRealPath(), [
        'folder' => 'posts',
    ]);
    $data['image'] = $uploaded->getSecurePath();
}
    $post->update($data);

    return redirect()->route('posts.show', $post->slug)->with('success', 'แก้ไขบทความเรียบร้อยแล้ว!');
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
