<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            if (!auth()->check()) return response()->json([
                "message" => "Unauthorized",
                "status" => 401,
            ]);
            $customerId = auth()->id();
            $getAllBookingQuery = Booking::with(['event', 'event.eventStatus', 'bookingStatus'])
                ->where('customer_id', $customerId)
                ->orderBy('booking_id', 'desc')
                ->get()
                //loop and hide event_status in every object
                ->each(function ($booking) {
                    $booking->setHidden(['customer_id', 'booking_status_id']);
                    if ($booking->event) {
                        $booking->event->setHidden(['event_status_id']);
                    }
                });
            return response()->json([
                "status" => 200,
                "message" => "success",
                "data" => BookingResource::collection($getAllBookingQuery),
            ]);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function adminIndex(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    "message" => "Unauthorized",
                    "status" => 401,
                ]);
            }

            $bookingStatusId = $request->query('booking_status_id');
            $eventName = $request->query('event_name'); // still searching inside event

            // Build the query
            $query = Booking::with(['event.eventStatus', 'bookingStatus'])
                ->orderBy('booking_id', 'desc');

            // Optional filter: booking_status_id
            if (!empty($bookingStatusId)) {
                $query->where('booking_status_id', $bookingStatusId);
            }

            // Optional filter: event_name (search inside related event)
            if (!empty($eventName)) {
                $query->whereHas('event', function ($q) use ($eventName) {
                    $q->where('event_name', 'like', '%' . $eventName . '%');
                });
            }

            $bookings = $query->get();

            // Hide sensitive fields
            $bookings->each(function ($booking) {
                $booking->setHidden(['customer_id', 'booking_status_id']);
                if ($booking->event) {
                    $booking->event->setHidden(['event_status_id']);
                }
            });

            return response()->json([
                "status" => 200,
                "message" => "success",

                "data" => BookingResource::collection($bookings),
//                "data" => $bookings,
            ]);

        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }


    //add middleware to level controller
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Store a newly created resource in storage.
     */
    /*public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        try {
            //check user is authorized if auth return true
//            if (!auth()->check()) return response()->json(['error' => 'Unauthorized'], 401);
            $validateEvent = Validator::make($request->all(), [
                'event_id' => 'required|integer',
                'booking_status_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',

            ]);

            if ($validateEvent->fails()) return response()->json(['errors' => $validateEvent->errors()], status: 401);
//            $customerId = $request->user()->id;
            $customerId = auth()->id();

            if (!$customerId) return response()->json([
                'error' => "sdasd",
                'data' => null
            ]);
            $event = Event::query()->findOrFail($request->get('event_id'));
            $totalAmount = $event->price * $request->get('quantity');
            Booking::query()->create([
                'event_id' => $request->get('event_id'),
                'quantity' => $request->get('quantity'),
                'booking_status_id' => $request->get('booking_status_id'),
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
            ]);
            return response()->json([
                'message' => 'Booking created successfully',
                'status' => 201,
                'data' => null
            ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }*/

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        try {
            $validator = Validator::make($request->all(), [
                'event_id' => 'required|integer|exists:event,event_id',
                'booking_status_id' => 'required|integer|exists:booking_status,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => 422
                ]);
            }

            return DB::transaction(function () use ($request) {
                $event = Event::query()->lockForUpdate()->findOrFail($request->event_id);

                // Check ticket availability
                if ($event->available_ticket < $request->quantity) {
                    Log::warning("Booking failed - Requested: {$request->quantity}, Available: {$event->available_ticket}, Event ID: {$request->event_id}");

                    return response()->json([
                        'message' => 'Not enough available tickets',
                        'status' => 400
                    ], 400);
                }

                // Create booking
                $booking = Booking::query()->create([
                    'event_id' => $request->event_id,
                    'quantity' => $request->quantity,
                    'booking_status_id' => $request->booking_status_id,
                    'customer_id' => auth()->id(),
                    'total_amount' => $event->price * $request->quantity,
                ]);

                // Update available tickets
                $event->decrement('available_ticket', $request->quantity);

                return response()->json([
                    'status' => 201,
                    'message' => 'Booking created successfully',
                    'data' => $this->formatBooking($booking),
                ]);
            });

        } catch (\Exception $e) {
            return $this->handleError($e);
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
     * Update the specified resource in storage.
     */
    public function update(string $id, Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status' => 401,
                ]);
            }
            $validated = $request->validate([
                'status_id' => 'required|integer|in:2,3' // Only allow 2 (Complete) or 3 (Cancel)
            ]);

            return DB::transaction(function () use ($id, $validated) {
                $booking = Booking::with('event')
                    ->where('booking_id', $id)
                    ->firstOrFail();

                $previousStatus = $booking->booking_status_id;
                $newStatus = $validated['status_id'];

                // Prevent invalid transitions
                if ($previousStatus === $newStatus) {
                    return response()->json([
                        'message' => 'Booking already has this status',
                        'status' => 400
                    ], 400);
                }

                // Update status
                $booking->update(['booking_status_id' => $newStatus]);

                // Handle ticket availability
                if ($newStatus === 3 && $booking->event) { // Cancelled
                    $booking->event->increment('available_ticket', $booking->quantity);
                    $payment = Payment::query()->where('booking_id', $booking->booking_id)->first();
                    if (!$payment) {
                        throw new \Exception("Payment not found for refund");
                    }
                    Refund::query()->create([
                        'booking_id' => $booking->booking_id,
                        'payment_id' => $payment->payment_id,
                        'refund_amount' => $payment->amount,
                        'user_id' => auth()->id(),
                        'refund_date' => now(),
                        'reason' => 'Cancelled by user',
                        'refund_status_id' => 1,
                    ]);
                }

                return response()->json([
                    'message' => sprintf(
                        'Booking %s successfully',
                        $newStatus === 2 ? 'completed' : 'cancelled'
                    ),
                    'status' => 200,
//                    'data' => $this->formatBooking($booking)
                    'data' => null
                ]);
            });

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Booking not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function handleError(\Exception $e): \Illuminate\Http\JsonResponse
    {
        report($e); // Log the error
        return response()->json([
            'error' => 'An error occurred',
            'message' => $e->getMessage(),
            'status' => 500
        ], 500);
    }

    protected function formatBooking($booking): array
    {
        return [
            'booking_id' => $booking->booking_id,
            'quantity' => $booking->quantity,
            'total_amount' => $booking->total_amount,
            'created_at' => $booking->created_at,
            'event' => $booking->event ? [
                'event_id' => $booking->event->event_id,
                'event_name' => $booking->event->event_name,
                'price' => $booking->event->price,
                'available_tickets' => $booking->event->available_tickets,
                'event_status' => $booking->event->eventStatus,
                'thumbnail' => $booking->event->image ? url('storage/' . $booking->event->image) : null,
            ] : null,
            'booking_status' => $booking->bookingStatus,
        ];
    }
}
