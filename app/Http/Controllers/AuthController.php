<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:customer,username',
            'email' => 'required|email|unique:customer,email',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string',
            'profile_image' => 'nullable|string',
            'role_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        // Create new customer
        $customer = Customer::query()->create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->firstname,
            'last_name' => $request->lastname,
            'phone_number' => $request->phonenumber,
            'profile_image' => $request->profile_image,
            'role_id' => $request->role_id, // Set role_id based on input
        ]);

        // Generate token for the newly registered user
        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'status'=> 200,
            'data' => [
                'customer' => $customer,
                'token' => $token
            ],
        ], 201);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $loginUserData = $request->validate([
                'username' => 'required|string',
                'email' => 'required|string|email',
                'password' => 'required|min:8'
            ]);
            $user = Customer::query()->where('username', $loginUserData['username'])->first();
            if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'status'=> 200,
                'data' => [
                    'customer' => $user,
                    'token' => $token
                ],

            ]);
        } catch (\Exception $exception) {
            Log::error('Registration Error: ' . $exception->getMessage());
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ], 500);
        }
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'message' => 'Logout successful',
                'status'=> 200,
                'data' => null,

            ]);
        }catch (\Exception $exception){
            $request->user()->tokens()->delete();
            return response()->json([
                'message' => 'Failed',
                'status'=> 200,
                'data'=> null

            ]);
        }
    }
}
