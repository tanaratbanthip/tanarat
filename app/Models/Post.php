<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'image',
    ];

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
}