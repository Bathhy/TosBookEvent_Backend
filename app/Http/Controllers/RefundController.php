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
                'refundStatus',
            ])->
            orderBy('refund_id')->get()->makeHidden(['user_id', ]);
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

    public function indexByUser(Request $request): JsonResponse
    {
        try {
            $statusIdQuery = $request->query('refund_status_id');
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status' => 401,
                ]);
            }
            $customerId = auth()->id();
            $getAllRefunds = Refund::with([
                'user',
                'status',
                'bookings.event'
            ])->where('user_id', $customerId)->orderBy('refund_id');
            if(!empty($statusIdQuery)){
                $getAllRefunds = $getAllRefunds->where('refund_status_id', $statusIdQuery);
            }
            $ListRefunds= $getAllRefunds->get()->makeHidden(['user_id', 'status']);
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $ListRefunds,
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
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status' => 401,
                ]);
            }

            $validate = $request->validate([
                'refund_status_id' => 'required|integer|in:2,3',
            ]);

            $findRefundId = Refund::query()->find($id);

            if (!$findRefundId) {
                return response()->json([
                    'message' => 'Refund not found',
                    'status' => 404,
                    'data' => null,
                ], 404);
            }

            // Fixed: Use consistent column name (refund_status_id)
            if ($findRefundId->refund_status_id == $validate['refund_status_id']) {
                return response()->json([
                    'message' => 'Refund already has this status',
                    'status' => 400,
                ], 400);
            }

            $findRefundId->update(['refund_status_id' => $validate['refund_status_id']]);

            // Fixed: Use consistent column name (refund_status_id)
            if ($findRefundId->refund_status_id == 2) {
                if ($findRefundId->payment) {
                    $findRefundId->payment->update(['payment_status_id' => '2']);
                }
            }

            $findRefundId->load(['user', 'refundStatus'])
                ->makeHidden(['user_id', ]);

            return response()->json([
                'status' => 200,
                'message' => $validate['refund_status_id'] == 2 ? 'Refund approved successfully' : 'Refund rejected successfully',
                'data' => $findRefundId
            ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /*   public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
       {
           try {
               if (!auth()->check()) {
                   return response()->json([
                       'message' => 'Unauthorized',
                       'status' => 401,
                   ]);
               }
               $validate = $request->validate([
                   'refund_status_id' => 'required|integer|in:2,3',
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
               if ($findRefundId->status_id == $validate['refund_status_id']) {
                   return response()->json([
                       'message' => 'Refund already has this status',
                       'status' => 400,
                   ], 400);
               }
               $findRefundId->update(['refund_status_id' => $validate['refund_status_id']]);
               if ($findRefundId->status_id == 2) {
                   if ($findRefundId->payment) {
                       $findRefundId->payment->update(['payment_status_id' => '2']);
                   }

               }
   //            $findRefundId->refresh();
               $findRefundId->load(['user', 'refundStatus'])->makeHidden(['user_id', 'refund_status_id']);
               return response()->json([
                   'status' => 200,
                   'message' => $validate['refund_status_id'] == 2 ? 'Refund approved successfully' : 'Refund rejected successfully',
                   'data' => $findRefundId
               ]);
           } catch (\Exception $exception) {
               return response()->json(['error' => $exception->getMessage()], 500);
           }
       }*/

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
