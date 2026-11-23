<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'key',
        'name',
        'description',
        'color',
        'is_admin',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Roles that are part of the system and cannot be deleted
     */
    public static function protectedKeys(): array
    {
        return ['super_admin', 'admin'];
    }
}
