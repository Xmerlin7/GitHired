<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->authService->register($validated);

        return response()->json([
            'success' => true,
            'message' => 'Signed Up Successfully',
            'data' => $result,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->authService->login($validated);

        return response()->json([
            'success' => true,
            'message' => 'Logged In Successfully',
            'data' => $result,
        ], 200);
    }
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged ou Successfully'
        ], 200);
    }
    public function logoutAllDevices(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged ou Successfully'
        ], 200);
    }
}
