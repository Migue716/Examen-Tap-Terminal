<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Profile;
use App\Services\AuditLogService;
use App\Services\CodeGeneratorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private CodeGeneratorService $codeGenerator,
        private AuditLogService $auditLog,
    ) {}

    public function index(): JsonResponse
    {
        $profiles = Profile::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $profiles->map(fn ($p) => ApiResponse::profile($p->toArray(), false)),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $profile = Profile::findOrFail($id);
        $data = $profile->toArray();
        $data['sections'] = $profile->sections()->map->toArray()->all();

        return response()->json(['data' => ApiResponse::profile($data, true)]);
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $profile = Profile::create([
            'code' => $this->codeGenerator->next('profiles', 'PFL'),
            ...$request->validated(),
        ]);

        $this->auditLog->log('profiles', (string) $profile->_id, 'create', null, $profile->toArray());

        return response()->json(['data' => ApiResponse::profile($profile->toArray(), false)], 201);
    }

    public function update(UpdateProfileRequest $request, string $id): JsonResponse
    {
        $profile = Profile::findOrFail($id);
        $previous = $profile->toArray();
        $profile->update($request->validated());
        $this->auditLog->log('profiles', (string) $profile->_id, 'update', $previous, $profile->fresh()->toArray());

        return response()->json(['data' => ApiResponse::profile($profile->fresh()->toArray(), false)]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $profile = Profile::findOrFail($id);
        $previous = $profile->toArray();
        $profile->delete();
        $this->auditLog->log('profiles', $id, 'delete', $previous, null);

        return response()->json(['message' => 'Perfil eliminado.']);
    }
}
