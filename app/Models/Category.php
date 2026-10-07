<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // ความสัมพันธ์: หมวดหมู่หนึ่ง มีบทความได้หลายบทความ (One-to-Many)
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}