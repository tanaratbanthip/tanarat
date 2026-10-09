<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        $userId = $request->user()?->id;
        $ip = $request->ip();

        $query = $post->likes();
        if ($userId) {
            $like = $query->where('user_id', $userId)->first();
        } else {
            $like = $query->whereNull('user_id')->where('ip_address', $ip)->first();
        }

        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create([
                'user_id' => $userId,
                'ip_address' => $ip,
            ]);
        }

        return back();
    }
}