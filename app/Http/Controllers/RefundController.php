<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefundController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status' => 401,
                ]);
            }
            $getAllRefunds = Refund::with([
                'user',
                'status',
            ])->orderBy('refund_id')->get()->makeHidden(['user_id', 'status_id']);
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $getAllRefunds,
            ],
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'status' => 500,
                ],
                500
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status' => 401,
                ]);
            }
            $validate = $request->validate([
                'status_id' => 'required|integer|in:2,3',
            ]);
//            $findRefundId = Refund::query()->where('refund_id', $id)->first();
            $findRefundId = Refund::query()->find($id);
            if (!$findRefundId) {
                return response()->json([
                    'message' => 'Refund not found',
                    'status' => 404,
                    'data' => null,
                ], 404);
            }
            if ($findRefundId->status_id == $validate['status_id']) {
                return response()->json([
                    'message' => 'Refund already has this status',
                    'status' => 400,
                ], 400);
            }
            $findRefundId->update(['status_id' => $validate['status_id']]);
            if ($findRefundId->status_id == 2) {
                if ($findRefundId->payment) {
                    $findRefundId->payment->update(['payment_status_id' => '2']);
                }

            }
//            $findRefundId->refresh();
            $findRefundId->load(['user', 'status'])->makeHidden(['user_id', 'status_id']);
            return response()->json([
                'status' => 200,
                'message' => $validate['status_id'] == 2 ? 'Refund approved successfully' : 'Refund rejected successfully',
                'data' => $findRefundId
            ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
