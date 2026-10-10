<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Cloudinary\Cloudinary;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Posts/Index', [
            'posts' => Post::with(['category', 'user'])->latest()->paginate(5),
        ]);
    }

    public function create()
    {
        return Inertia::render('Posts/Create', [
            'categories' => Category::all(),
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
            ? auth()->user()->bookmarkedPosts()->where('posts.id', $post->id)->exists()
            : false;

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
        Gate::authorize('update', $post);

        return Inertia::render('Posts/Edit', [
            'post' => $post,
            'categories' => Category::all(),
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
        $data['slug'] = Str::slug($request->title) . '-' . uniqid();

        if ($request->hasFile('image')) {
            $cloudinaryUrl = env('CLOUDINARY_URL') ?: config('cloudinary.cloud_url');

            if ($cloudinaryUrl) {
                try {
                    $cloudinary = new Cloudinary($cloudinaryUrl);
                    $response = $cloudinary->uploadApi()->upload(
                        $request->file('image')->getRealPath(),
                        [
                            'folder' => 'posts',
                            'resource_type' => 'image',
                            'transformation' => [
                                [
                                    'width' => 1200,
                                    'crop' => 'limit',
                                    'quality' => 'auto',
                                    'fetch_format' => 'auto',
                                ],
                            ],
                        ]
                    );
                    $data['image'] = $response['secure_url'];
                } catch (\Exception $e) {
                    return back()->with('error', 'ไม่สามารถอัปโหลดภาพหน้าปกได้: ' . $e->getMessage());
                }
            }
        }

        $post = Post::create($data);

        return redirect()->route('posts.show', $post->slug)->with('success', 'สร้างบทความเรียบร้อยแล้ว!');
    }

    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:3072',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $cloudinaryUrl = env('CLOUDINARY_URL') ?: config('cloudinary.cloud_url');

            if ($cloudinaryUrl) {
                try {
                    $cloudinary = new Cloudinary($cloudinaryUrl);

                    // 1. ลบรูปเดิมออกจาก Cloudinary ก่อนอัปโหลดรูปใหม่
                    if ($post->image && str_starts_with($post->image, 'http')) {
                        $this->deleteCloudinaryImage($cloudinary, $post->image);
                    }

                    // 2. อัปโหลดรูปใหม่พร้อมบีบอัดและปรับขนาด 1200 px
                    $response = $cloudinary->uploadApi()->upload(
                        $request->file('image')->getRealPath(),
                        [
                            'folder' => 'posts',
                            'resource_type' => 'image',
                            'transformation' => [
                                [
                                    'width' => 1200,
                                    'crop' => 'limit',
                                    'quality' => 'auto',
                                    'fetch_format' => 'auto',
                                ],
                            ],
                        ]
                    );
                    $data['image'] = $response['secure_url'];
                } catch (\Exception $e) {
                    return back()->with('error', 'ไม่สามารถอัปโหลดภาพหน้าปกได้: ' . $e->getMessage());
                }
            }
        }

        $post->update($data);

        return redirect()->route('posts.show', $post->slug)->with('success', 'แก้ไขบทความเรียบร้อยแล้ว!');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        if ($post->image) {
            if (str_starts_with($post->image, 'http')) {
                $cloudinaryUrl = env('CLOUDINARY_URL') ?: config('cloudinary.cloud_url');
                if ($cloudinaryUrl) {
                    try {
                        $cloudinary = new Cloudinary($cloudinaryUrl);
                        $this->deleteCloudinaryImage($cloudinary, $post->image);
                    } catch (\Exception $e) {
                        // ข้ามข้อผิดพลาดเพื่อไม่ให้ขัดขวางการลบโพสต์ในฐานข้อมูล
                    }
                }
            } else {
                Storage::disk('public')->delete($post->image);
            }
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'ลบบทความเรียบร้อยแล้ว');
    }

    /**
     * ดึง public_id และลบรูปภาพจาก Cloudinary
     */
    private function deleteCloudinaryImage(Cloudinary $cloudinary, string $imageUrl): void
    {
        if (preg_match('#/upload/(?:(?:[a-zA-Z]_[^/,]+,?)+/)?(?:v\d+/)?(.+?)(?:\.[a-zA-Z0-9]+)?$#', $imageUrl, $matches)) {
            $publicId = $matches[1];
            $cloudinary->uploadApi()->destroy($publicId);
        }
    }
}
