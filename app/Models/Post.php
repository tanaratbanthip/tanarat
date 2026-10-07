<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'category_id'];
    // ความสัมพันธ์: บทความนี้ สังกัดอยู่ในหมวดหมู่อันหนึ่ง (BelongsTo)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
