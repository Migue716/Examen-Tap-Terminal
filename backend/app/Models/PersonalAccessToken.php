<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use MongoDB\Laravel\Eloquent\DocumentModel;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use DocumentModel;

    protected $connection = 'mongodb';

    protected $collection = 'personal_access_tokens';

    protected $primaryKey = '_id';

    /** MongoDB ObjectId strings; Sanctum valida el prefijo del token según keyType. */
    protected $keyType = 'string';

    public $incrementing = false;
}
