<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\UserValidationService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepository;
    protected $validationService;

    public function __construct(UserRepository $userRepository, UserValidationService $validationService)
    {
        $this->userRepository = $userRepository;
        $this->validationService = $validationService;
    }

    public function signup(Request $request)
    {
        $data = $request->all();
        $validationResult = $this->validationService->validationSignupRequest($data);

        if(!$validationResult['status']) {
            return response()->json($validationResult, 422);
        }

        return $this->userRepository->signup($data);
    }

    public function login(Request $request)
    {
        $data = $request->all();
        
        $validationResult = $this->validationService->validateLoginRequest($data);
        
        if(!$validationResult['status']) {
            return response()->json($validationResult, 422);
        }

        return $this->userRepository->login($data);

    }

    public function verifyToken(Request $request)
    {
        $data = $request->all();
        
        if (!isset($data['api_token'])) {
            return response()->json([
                'status' => false,
                'message' => 'api_token is required',
                'data' => [],
            ], 422);
        }
        return $this->userRepository->verifyToken($data);
    }
}
