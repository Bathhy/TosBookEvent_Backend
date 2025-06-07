<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    // List all reports
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            // Get first and last day of current month
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->endOfMonth()->toDateString();

            // Try to find existing report for current month
            $report = Report::query()
                ->where('date_from', $dateFrom)
                ->where('date_to', $dateTo)
                ->first();

            if (!$report) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No report found for current month. Please generate a report first.',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Current month report retrieved successfully',
                'data' => $report,
            ]);

        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    // Show a specific report
    public function show($id)
    {
        $report = Report::findOrFail($id);
        return response()->json($report);
    }

    // Generate a new report
    public function generate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
            ]);

            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            $totalBooking = Booking::query()->whereBetween('booking_date', [$dateFrom, $dateTo])->count();
            $totalIncome = Payment::query()->whereBetween('payment_date', [$dateFrom, $dateTo])->sum('amount');
            $totalRefunds = Refund::query()->whereBetween('refund_date', [$dateFrom, $dateTo])->sum('refund_amount') ?? 0;
            Log::warning(message: $totalRefunds);
            $netIncome = $totalIncome - $totalRefunds;

            $report = Report::query()->create([
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'total_booking' => $totalBooking,
                'total_income' => $totalIncome,
                'total_refunds' => $totalRefunds,
                'net_income' => $netIncome,
                'report_create' => now(),
            ]);

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Report generated successfully',
                    'data' => $report,
                ]
                , 201
            );
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    // Filter by month or week
    public function filter(Request $request)
    {
        if ($request->has('month')) {
            // Filter by month (e.g. "2025-06")
            $month = $request->input('month');
            $reports = Report::whereYear('date_from', substr($month, 0, 4))
                ->whereMonth('date_from', substr($month, 5, 2))
                ->get();
        } elseif ($request->has(['start', 'end'])) {
            // Filter by week (or custom range)
            $start = $request->input('start');
            $end = $request->input('end');
            $reports = Report::whereBetween('date_from', [$start, $end])->get();
        } else {
            return response()->json(['error' => 'Invalid filter parameters'], 400);
        }

        return response()->json($reports);
    }
}
