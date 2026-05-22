<?php

namespace App\Support;

use Carbon\Carbon;

class ApiResponse
{
    public static function formatDate(?Carbon $date): ?string
    {
        return $date?->format('d/m/Y H:i');
    }

    public static function product(array $product): array
    {
        return [
            'id' => (string) ($product['_id'] ?? $product['id'] ?? ''),
            'code' => $product['code'] ?? '',
            'name' => $product['name'] ?? '',
            'brand' => $product['brand'] ?? '',
            'price' => $product['price'] ?? 0,
            'created_at' => isset($product['created_at'])
                ? self::formatDate($product['created_at'] instanceof Carbon ? $product['created_at'] : Carbon::parse($product['created_at']))
                : null,
        ];
    }

    public static function user(array $user, bool $detailed = false): array
    {
        $base = [
            'id' => (string) ($user['_id'] ?? $user['id'] ?? ''),
            'code' => $user['code'] ?? '',
            'username' => $user['username'] ?? '',
            'name' => $user['name'] ?? '',
            'created_at' => isset($user['created_at'])
                ? self::formatDate($user['created_at'] instanceof Carbon ? $user['created_at'] : Carbon::parse($user['created_at']))
                : null,
        ];

        if (! $detailed) {
            return $base;
        }

        $profileIds = $user['profile_ids'] ?? [];

        return array_merge($base, [
            'phone' => $user['phone'] ?? null,
            'profile_photo' => $user['profile_photo'] ?? null,
            'profiles' => collect($user['profiles'] ?? [])->map(fn ($p) => self::profile($p, false))->values(),
        ]);
    }

    public static function profile(array $profile, bool $detailed = true): array
    {
        $base = [
            'id' => (string) ($profile['_id'] ?? $profile['id'] ?? ''),
            'code' => $profile['code'] ?? '',
            'name' => $profile['name'] ?? '',
            'created_at' => isset($profile['created_at'])
                ? self::formatDate($profile['created_at'] instanceof Carbon ? $profile['created_at'] : Carbon::parse($profile['created_at']))
                : null,
        ];

        if (! $detailed) {
            return $base;
        }

        return array_merge($base, [
            'sections' => collect($profile['sections'] ?? [])->map(fn ($s) => [
                'id' => (string) ($s['_id'] ?? $s['id'] ?? ''),
                'code' => $s['code'] ?? '',
                'name' => $s['name'] ?? '',
                'module' => $s['module'] ?? '',
            ])->values(),
        ]);
    }
}
