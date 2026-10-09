<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookmarkController;
// เส้นทางหน้าแรก รองรับ Search, Category Filter, และ Sort
Route::get('/', function (Request $request) {
    $query = Post::with(['category', 'user']);

    // 1. ค้นหาตามชื่อเรื่อง (title) หรือเนื้อหา (content)
    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    // 2. กรองตามหมวดหมู่ (category_id)
    if ($category = $request->input('category')) {
        $query->where('category_id', $category);
    }

    // 3. เรียงลำดับบทความ (sort: latest / oldest)
    if ($request->input('sort') === 'oldest') {
        $query->oldest();
    } else {
        $query->latest();
    }

    return Inertia::render('Welcome', [
        'posts' => $query->paginate(6)->withQueryString(), // withQueryString คงค่า filter ไว้เวลาเปลี่ยนหน้า pagination
        'categories' => Category::all(),
        'filters' => $request->only(['search', 'category', 'sort']),
    ]);
})->name('home');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::middleware('auth')->group(function () {
    // แดชบอร์ดวิเคราะห์สถิติ
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // จัดการหมวดหมู่
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Posts & Bookmarks & Profiles เดิม...
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('posts.bookmark');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\CommentController;

Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
require __DIR__.'/auth.php';

use App\Http\Controllers\SeoController;

// SEO & Syndication Routes (ไม่ต้องผ่าน Auth Middleware)
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/feed', [SeoController::class, 'feed'])->name('seo.feed');