<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'entity',
        'entity_id',
        'action',
        'previous_data',
        'current_data',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'previous_data' => 'array',
        'current_data' => 'array',
        'created_at' => 'datetime',
    ];
}
