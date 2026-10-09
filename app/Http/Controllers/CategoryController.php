<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->latest()->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
        ]);

        Category::create($validated);

        return back()->with('success', 'เพิ่มหมวดหมู่ใหม่เรียบร้อยแล้ว');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return back()->with('success', 'อัปเดตชื่อหมวดหมู่สำเร็จ');
    }

    public function destroy(Category $category)
    {
        // ป้องกันไม่ให้ลบหมวดหมู่ที่มีบทความอยู่
        if ($category->posts()->exists()) {
            return back()->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีบทความใช้งานอยู่');
        }

        $category->delete();

        return back()->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
    }
}