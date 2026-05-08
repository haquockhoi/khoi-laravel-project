<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Model
{
    protected $fillable = [
        'user_group_id',
        'controller',
        'action',
    ];

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }
}