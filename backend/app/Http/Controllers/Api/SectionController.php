<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\JsonResponse;

class SectionController extends Controller
{
    public function index(): JsonResponse
    {
        $sections = Section::orderBy('name')->get();

        return response()->json([
            'data' => $sections->map(fn ($s) => [
                'id' => (string) $s->_id,
                'code' => $s->code,
                'name' => $s->name,
                'module' => $s->module,
                'can_write' => $s->can_write,
            ]),
        ]);
    }
}
