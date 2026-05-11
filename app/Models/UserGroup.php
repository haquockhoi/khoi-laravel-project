<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_fullaccess',
    ];

    protected $casts = [
        'is_fullaccess' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->hasMany(UserGroupPermission::class);
    }
}