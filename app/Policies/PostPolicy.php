<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    // ทุกคนสามารถดูรายการและอ่านโพสต์ได้
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    // ต้องล็อกอินถึงจะสร้างบทความได้
    public function create(User $user): bool
    {
        return true;
    }

    // เจ้าของบทความเท่านั้นที่แก้ไขได้
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // เจ้าของบทความเท่านั้นที่ลบได้
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}