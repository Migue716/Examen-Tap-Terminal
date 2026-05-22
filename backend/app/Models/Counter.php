<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Counter extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'counters';

    protected $fillable = ['key', 'seq'];

    protected $casts = [
        'seq' => 'integer',
    ];
}
