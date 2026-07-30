<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'token',
        'token_expires_at',
        'role',
    ];

    protected $attributes = [
        'role' => 'viewer',
    ];

    protected $hidden = [
        'password',
        'token',
        'token_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'token_expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
