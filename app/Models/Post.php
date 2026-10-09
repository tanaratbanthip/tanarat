<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    ];
public function user()
    {
        return $this->belongsTo(User::class);
    }
    // สร้าง slug อัตโนมัติทุกครั้งที่บันทึก title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title) . '-' . Str::random(5);
            }
        });
    }

public function resolveRouteBinding($value, $field = null)
{
    return $this->where('slug', $value)
                ->orWhere('id', $value)
                ->firstOrFail();
}

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
public function comments()
{
    return $this->hasMany(Comment::class)->latest();
}
    
}