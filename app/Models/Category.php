<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function news()
    {
        return $this->belongsToMany(News::class, 'category_news')
            ->withTimestamps();
    }
}