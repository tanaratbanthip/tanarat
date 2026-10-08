<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Post;
use App\Models\Category;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'posts' => Post::with('category')->latest()->paginate(5),
        'categories' => Category::all(),
    ]);
})->name('home');

// --- เส้นทางสำหรับ Blog / Posts ---
// 1. รายการบทความทั้งหมด
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// 2. ฟอร์มสร้างบทความ และการบันทึก (ต้องอยู่ก่อน {post})
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

// 3. ดูรายละเอียดบทความตาม id
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// 4. แก้ไขบทความ
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');

// 5. ลบบทความ
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

// --- หน้า Dashboard และ Auth ---
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';