<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'category_id',
        'image',
        'views',
    ];

    // โหลด reading_time เข้ามาพร้อมข้อมูลโพสต์เสมอ
    protected $appends = ['reading_time'];

    // คำนวณเวลาอ่านโดยประมาณ (นาที)
    protected function readingTime(): Attribute
    {
        return Attribute::make(
            get: function () {
                $plainText = strip_tags($this->content ?? '');
                $charCount = mb_strlen($plainText, 'UTF-8');
                // อัตราการอ่านเฉลี่ยประมาณ 500 ตัวอักษร/นาที (ขั้นต่ำ 1 นาที)
                return max(1, (int) ceil($charCount / 500));
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
                    ->whereNull('parent_id')
                    ->with(['user', 'replies.user'])
                    ->latest();
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)
                    ->orWhere('id', $value)
                    ->firstOrFail();
    }

    public function likes()
{
    return $this->hasMany(Like::class);
}
}