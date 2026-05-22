<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Section extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'sections';

    protected $fillable = [
        'code',
        'name',
        'module',
        'can_write',
    ];

    protected $casts = [
        'can_write' => 'boolean',
    ];
}
