<?php

namespace App\Services;

use App\Models\Counter;

class CodeGeneratorService
{
    public function next(string $key, string $prefix): string
    {
        $counter = Counter::firstOrCreate(['key' => $key], ['seq' => 0]);
        $counter->increment('seq');

        return $prefix.str_pad((string) $counter->seq, 6, '0', STR_PAD_LEFT);
    }
}
