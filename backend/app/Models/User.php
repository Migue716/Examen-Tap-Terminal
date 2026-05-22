<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $fillable = [
        'code',
        'name',
        'username',
        'phone',
        'profile_photo',
        'password',
        'profile_ids',
        'is_admin',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'profile_ids' => 'array',
        'is_admin' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function profiles()
    {
        return Profile::whereIn('_id', $this->profile_ids ?? [])->get();
    }

    public function allowedSectionKeys(bool $writeOnly = false): array
    {
        if ($this->is_admin) {
            $query = Section::query();
            if ($writeOnly) {
                $query->where('can_write', true);
            }

            return $query->pluck('module')->unique()->values()->all();
        }

        $profiles = $this->profiles();
        $sectionIds = collect($profiles)->flatMap(fn ($p) => $p->section_ids ?? [])->unique()->values();

        $sections = Section::whereIn('_id', $sectionIds->all())->get();

        if ($writeOnly) {
            $sections = $sections->where('can_write', true);
        }

        return $sections->pluck('module')->unique()->values()->all();
    }

    public function canAccessSection(string $module, bool $requiresWrite = false): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $profiles = $this->profiles();
        $sectionIds = collect($profiles)->flatMap(fn ($p) => $p->section_ids ?? [])->unique();

        $sections = Section::whereIn('_id', $sectionIds->all())
            ->where('module', $module)
            ->get();

        if ($sections->isEmpty()) {
            return false;
        }

        if (! $requiresWrite) {
            return true;
        }

        return $sections->contains(fn ($s) => $s->can_write);
    }
}
