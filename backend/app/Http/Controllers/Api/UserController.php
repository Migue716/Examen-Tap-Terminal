<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\CodeGeneratorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private CodeGeneratorService $codeGenerator,
        private AuditLogService $auditLog,
    ) {}

    public function index(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $users->map(fn ($u) => ApiResponse::user($u->toArray())),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $data = $user->toArray();
        $data['profiles'] = $user->profiles()->map->toArray()->all();

        return response()->json(['data' => ApiResponse::user($data, true)]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'code' => $this->codeGenerator->next('users', 'USR'),
            'name' => $validated['name'],
            'username' => $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'profile_photo' => $validated['profile_photo'],
            'profile_ids' => $validated['profile_ids'] ?? [],
            'password' => Hash::make($validated['password'] ?? 'TapTerminal123'),
        ]);

        $this->auditLog->log('users', (string) $user->_id, 'create', null, $user->toArray());

        return response()->json(['data' => ApiResponse::user($user->toArray())], 201);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $previous = $user->toArray();
        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        $this->auditLog->log('users', (string) $user->_id, 'update', $previous, $user->fresh()->toArray());

        return response()->json(['data' => ApiResponse::user($user->fresh()->toArray())]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $previous = $user->toArray();
        $user->delete();
        $this->auditLog->log('users', $id, 'delete', $previous, null);

        return response()->json(['message' => 'Usuario eliminado.']);
    }
}
