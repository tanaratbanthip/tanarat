<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookmarkController extends Controller
{
    // แสดงรายการบทความทั้งหมดที่ผู้ใช้บันทึกไว้
    public function index(Request $request)
    {
        $bookmarkedPosts = $request->user()
            ->bookmarkedPosts()
            ->with(['category', 'user'])
            ->paginate(6);

        return Inertia::render('Bookmarks/Index', [
            'posts' => $bookmarkedPosts,
        ]);
    }

    // สลับสถานะ บันทึก / ยกเลิกการบันทึก
    public function toggle(Request $request, Post $post)
    {
        $user = $request->user();
        $isBookmarked = $user->bookmarkedPosts()->where('post_id', $post->id)->exists();

        if ($isBookmarked) {
            $user->bookmarkedPosts()->detach($post->id);
            $message = 'ยกเลิกการบันทึกบทความแล้ว';
        } else {
            $user->bookmarkedPosts()->attach($post->id);
            $message = 'บันทึกบทความลงรายการโปรดเรียบร้อย!';
        }

        return back()->with('success', $message);
    }
}