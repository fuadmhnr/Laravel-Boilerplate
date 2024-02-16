<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;

interface UserRepository
{
    public function signup(array $data): JsonResponse;
    public function login(array $data): JsonResponse;
    public function verifyToken(array $data): JsonResponse;
}