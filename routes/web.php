<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SeoController;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LikeController;

// 1. เส้นทางสาธารณะ (Public Routes)
Route::get('/', function (Request $request) {
    $query = Post::with(['category', 'user']);

    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    if ($category = $request->input('category')) {
        $query->where('category_id', $category);
    }

    if ($request->input('sort') === 'oldest') {
        $query->oldest();
    } else {
        $query->latest();
    }

    // ดึงข้อมูลเจ้าของบล็อก (บัญชี Admin คนแรก หรือผู้ใช้คนแรกของระบบ)
    $author = \App\Models\User::where('role', 'admin')->first() ?? \App\Models\User::first();

    return Inertia::render('Welcome', [
        'posts' => $query->paginate(6)->withQueryString(),
        'categories' => Category::all(),
        'filters' => $request->only(['search', 'category', 'sort']),
        'author' => $author ? [
            'name' => $author->name,
            'avatar' => $author->avatar,
        ] : null,
    ]);
})->name('home');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// 2. เส้นทางที่ต้องล็อกอิน (Auth Required)
Route::middleware('auth')->group(function () {
    // ต้องวาง /posts/create ไว้ก่อน /posts/{post} เสมอ เพื่อไม่ให้ชนกับ Wildcard {post}
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // ระบบบุ๊กมาร์ก
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('posts.bookmark');

    // โปรไฟล์
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 3. เส้นทางเปิดอ่านบทความสาธารณะ (วางหลัง /posts/create)
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
// 4. เส้นทางเฉพาะผู้ดูแลระบบ (Admin Only)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// 5. SEO & Syndication Routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/feed', [SeoController::class, 'feed'])->name('seo.feed');

require __DIR__.'/auth.php';