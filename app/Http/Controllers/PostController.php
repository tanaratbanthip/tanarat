<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category; // 1. นำเข้า Model Category
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Posts/Index', [
            // ใช้ with('category') เพื่อดึงข้อมูลชื่อหมวดหมู่มาพร้อมกับบทความ (Eager Loading)
            'posts' => Post::with('category')->latest()->paginate(3)
        ]);
    }

    public function create()
    {
        return Inertia::render('Posts/Create', [
            // ส่งรายชื่อหมวดหมู่ทั้งหมดไปให้หน้าสร้างบทความใช้ทำ Dropdown
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id', // ต้องระบุ และ id ต้องมีอยู่จริง
        ]);

        Post::create($validated);

        return redirect()->route('posts.index');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Posts/Edit', [
            'post' => $post,
            'categories' => Category::all() // ส่งไปให้หน้าแก้ไขด้วย
            
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post->update($validated);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index');
    }

    // แสดงรายละเอียดบทความฉบับเต็ม
    public function show(Post $post)
    {
        // โหลดข้อมูล category ติดมาด้วย เพื่อให้รู้ว่าอยู่หมวดหมู่ไหน
        return Inertia::render('Posts/Show', [
            'post' => $post->load('category')
        ]);
    }
}