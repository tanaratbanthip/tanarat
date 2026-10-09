<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'author_name' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $authorName = $request->user() 
            ? $request->user()->name 
            : ($validated['author_name'] ?: 'ผู้เยี่ยมชม');

        $post->comments()->create([
            'user_id' => $request->user()?->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'author_name' => $authorName,
            'content' => $validated['content'],
        ]);

        $message = isset($validated['parent_id']) ? 'ตอบกลับความคิดเห็นเรียบร้อยแล้ว' : 'ส่งความคิดเห็นเรียบร้อยแล้ว!';
        return back()->with('success', $message);
    }
}