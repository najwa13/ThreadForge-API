<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @group Authentication
     *
     * Register a new user account.
     *
     * @bodyParam name string required The user's name. Example: John Doe
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The password (min 8 chars). Example: secret123
     * @bodyParam password_confirmation string required Must match password. Example: secret123
     *
     * @responseField message string The success message.
     * @responseField user object The created user.
     * @responseField token string The API token.
     *
     * @response 201 {
     *   "message": "Utilisateur créé avec succès.",
     *   "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "created_at": "2026-06-22T10:00:00.000000Z" },
     *   "token": "1|abc123..."
     * }
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * @group Authentication
     *
     * Login with email and password to receive an API token.
     *
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The user's password. Example: secret123
     *
     * @responseField message string The success message.
     * @responseField user object The authenticated user.
     * @responseField token string The API token.
     *
     * @response 200 {
     *   "message": "Connexion réussie.",
     *   "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "created_at": "2026-06-22T10:00:00.000000Z" },
     *   "token": "1|abc123..."
     * }
     *
     * @response 401 {
     *   "message": "Identifiants invalides."
     * }
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Identifiants invalides.',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * @group Authentication
     *
     * Logout and revoke the current access token.
     *
     * @response 200 {
     *   "message": "Déconnecté avec succès."
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnecté avec succès.',
        ]);
    }
}
