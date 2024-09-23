<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:191|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
                'data' => new UserResource($user),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $token = $user->createToken('API Token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'user' => new UserResource($user),
                    'token' => $token,
                ]);
            }

            return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);

        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'User logged out successfully']);
    }
}
