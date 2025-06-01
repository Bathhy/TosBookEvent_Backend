<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class DashboardController
{
    function index(): JsonResponse
    {
        try {
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => [
                    [
                        'type' => 'bookings',
                        'name' => 'Total Bookings',
                        'count' => Booking::query()->count(),

                    ],
                    [
                        'type' => 'events',
                        'name' => 'Total Events',
                        'count' => Event::query()->count(),

                    ],
                    [
                        'type' => 'users',
                        'name' => 'Total Users',
                        'count' => Customer::query()->count(),

                    ],
                ]
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message' => $exception->getMessage(),
                'data' => null,
            ]);
        }
    }
}
