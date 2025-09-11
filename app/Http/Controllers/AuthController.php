<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->loginSuccessResponse(new UserResource($user), 'Login realizado com sucesso', 200, ['access_token' => $token]);

        }

        return $this->errorResponse('Credenciais inválidas.', 401);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->loginSuccessResponse(new UserResource($user), 'Usuário registrado com sucesso', 201, ['access_token' => $token]);

    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            return $this->successResponse(null, 'Logout realizado com sucesso', 200);
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Logout realizado com sucesso.'
            // ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao realizar logout.', 500);
        }
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->loginSuccessResponse(new UserResource($user), 'Usuário autenticado', 200);
    }

}
