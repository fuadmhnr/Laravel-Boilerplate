<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\UserValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class EloquentUserRepository implements UserRepository
{
    protected $validationService;

    public function __construct(UserValidationService $validationService)
    {
        $this->validationService = $validationService;
    }
    public function signup(array $data): JsonResponse
    {
        $validationResult = $this->validationService->validationSignupRequest($data);

        if (!$validationResult['status']) {
            return response()->json($validationResult, 422);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registration account success',
            'data' => $user->toArray()
        ]);
    }

    public function login(array $data): JsonResponse
    {
        // Validation logic
        $validationResult = $this->validationService->validateLoginRequest($data);

        if (!$validationResult['status']) {
            return response()->json($validationResult, 422);
        }

        $user = $this->authenticateUser($data);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email does not exists in our credentials',
                'data' => []
            ], 401);
        }

        $isValidUser = Hash::check($data['password'], $user->password);

        if (!$isValidUser) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
                'data' => []
            ], 401);
        }

        $this->revokeExistingTokens($user);

        $token = $user->createToken('token', ['*'], now()->addDay())->plainTextToken;
        $user->api_token = $token;

        return response()->json($user, 200);
    }

    public function verifyToken(array $data): JsonResponse
    {
        $token = PersonalAccessToken::findToken($data['api_token']);
        $user = $token->tokenable;
        $user->api_token = $data['api_token'];

        return response()->json($user->toArray(), 200);
    }

    private function revokeExistingTokens(User $user)
    {
        $user->tokens()->where('tokenable_id', $user->id)->delete();
    }

    private function authenticateUser(array $data): ?User
    {
        return User::where('email', $data['email'])->first();
    }
}
