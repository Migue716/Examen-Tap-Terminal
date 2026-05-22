<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Profile extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'profiles';

    protected $fillable = [
        'code',
        'name',
        'section_ids',
    ];

    protected $casts = [
        'section_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sections()
    {
        return Section::whereIn('_id', $this->section_ids ?? [])->get();
    }
}
