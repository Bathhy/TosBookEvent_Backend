<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryUserManagementController extends UserManagementController
{
    public function getAllCategories(): \Illuminate\Http\JsonResponse
    {
        try {
            $categories = Category::query()->orderBy('category_id')->get();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function addCategory(Request $request): JsonResponse
    {
        try {
            $userAuth = Auth::guard('api')->user();
            if (!$userAuth || $userAuth->role_id !== 1) {
                return response()->json(['error' => 'Unauthorized access'], 401);
            }
            $addCategoryQuery = Category::query()->create([
                'category_name' => $request->get('category_name'),
                'category_icon' => $request->get('category_icon'),
            ]);
            return response()->json([
                'message' => 'Category added successfully',
                'status_code' => 201,
                'data' => $addCategoryQuery,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
