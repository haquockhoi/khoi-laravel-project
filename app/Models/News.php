<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'thumbnail',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_news')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}