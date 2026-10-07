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
// ดูรายละเอียดบทความตาม id
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// เส้นทางสำหรับแสดงบล็อก
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
// หน้าแสดงฟอร์มสร้างบทความ
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
// รับข้อมูลจากฟอร์มไปบันทึกลงฐานข้อมูล
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

// 1. หน้าแสดงฟอร์มแก้ไข พร้อมระบุ id ของโพสต์
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
// 2. รับข้อมูลที่แก้ไขแล้วไปอัปเดตในฐานข้อมูล (ใช้เมธอด put)
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
// รับคำสั่งลบบทความตาม id
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';