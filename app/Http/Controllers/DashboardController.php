<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posts' => Post::count(),
            'total_views' => Post::sum('views'),
            'total_comments' => Comment::count(),
            'total_categories' => Category::count(),
        ];

        // 5 อันดับบทความยอดวิวสูงสุด
        $topPosts = Post::with('category')
            ->orderByDesc('views')
            ->take(5)
            ->get(['id', 'title', 'slug', 'category_id', 'views', 'created_at']);

        // 5 ความคิดเห็นล่าสุด
        $recentComments = Comment::with(['post:id,title,slug', 'user:id,name'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'topPosts' => $topPosts,
            'recentComments' => $recentComments,
        ]);
    }
}