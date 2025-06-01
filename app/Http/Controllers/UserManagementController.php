<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class  UserManagementController extends Controller
{
    public function getAllUser(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $userNameQuery = $request->query('username');
            $coreQuery = Customer::query();
            if(!empty($userNameQuery)) {
                $coreQuery->whereRaw('username ILIKE ?', [$userNameQuery]);
            }
            $getAllCustomerQuery = $coreQuery->get();
            return response()->json(
                [
                    "status" => 200,
                    "message" => "success",
                    "data" => $getAllCustomerQuery
                ]
            );
        }catch (\Exception $e){
            return response()-> json([
                "status" => 500,
                "message" => $e->getMessage(),
            ]);
        }
    }

    public function deleteUserById(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $query = Customer::query()->where('customer_id', $id)->first();
            if(!$query){
                return response()-> json([
                    "status" => 500,
                    "message" => "Customer/User not found"
                ]);
            }
            $query->delete();
            return response()->json([
                "status" => 200,
                "message" => "success",
                "data" => null
            ]);
        }catch (\Exception $e){
            return response()-> json([
                "status" => 500,
                "message" => $e->getMessage(),
            ]);
        }
    }
}
