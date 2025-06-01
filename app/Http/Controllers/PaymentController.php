<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status' => 401,
                ]);
            }

            $validated = Validator::make($request->all(), [
                'booking_id' => 'required|integer|exists:booking,booking_id',
                'payment_method_id' => 'required|integer|exists:payment_method,payment_method_id',
                'amount' => 'required|numeric|min:0',
                'transaction_id' => 'required|string',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validated->errors(),
                ]);
            }

            $payment = Payment::create([
                'booking_id' => $request->booking_id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'user_id' => auth()->id(),
                'payment_date' => now(),
            ]);

            return response()->json([
                'message' => 'Payment created successfully',
                'status' => 201,
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
