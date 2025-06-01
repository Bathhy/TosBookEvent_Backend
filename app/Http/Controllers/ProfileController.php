<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class ProfileController
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $customerId = auth()->id();

            if (!$customerId) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 401);
            }
            $findUserProfile = Customer::query()->find($customerId);
            if (!$findUserProfile) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $findUserProfile
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

