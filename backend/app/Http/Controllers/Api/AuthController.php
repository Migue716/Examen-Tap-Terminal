<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->validated('username'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->authUserPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->authUserPayload($request->user())]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('username', $request->validated('username'))->first();

        if (! $user) {
            return response()->json(['message' => 'El usuario no existe en el sistema.'], 404);
        }

        $newPassword = Str::password(12, letters: true, numbers: true, symbols: true);

        $user->update(['password' => Hash::make($newPassword)]);

        try {
            Mail::to($user->username)->send(
                new PasswordResetMail($user->name, $user->username, $newPassword)
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'No se pudo enviar el correo. En Docker usa Mailpit (http://localhost:8025) y reinicia el backend.',
            ], 503);
        }

        return response()->json([
            'message' => 'Se enviaron las credenciales temporales al correo registrado. En desarrollo revísalas en Mailpit: http://localhost:8025',
        ]);
    }

    private function authUserPayload(User $user): array
    {
        return [
            'id' => (string) $user->_id,
            'code' => $user->code,
            'name' => $user->name,
            'username' => $user->username,
            'profile_photo' => $user->profile_photo,
            'sections' => $user->allowedSectionKeys(),
            'write_sections' => $user->allowedSectionKeys(true),
            'is_admin' => (bool) $user->is_admin,
        ];
    }
}
