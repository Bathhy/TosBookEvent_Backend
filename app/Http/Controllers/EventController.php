<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades;

class EventController extends Controller
{
    public function getAllEvent(Request $request): JsonResponse
    {
        try {
            $categoryIdQuery = $request->query('category_id');
            $event_status_Query = $request->query('event_status_id');
            $eventNameQuery = $request->query('event_name');

            $query = Event::query()
                ->join('event_status', 'event_status.id', '=', 'event.event_status_id')
                ->select(
                    'event.event_id',
                    'event.event_name',
                    'event.thumbnail',
                    'event.event_descriptions',
                    'event.price',
                    'event.date',
                    'event.location',
                    'event.total_ticket',
                    'event.available_ticket',
                    'event.category_id',
                    'event_status.name AS status'
                );

            if (!empty($categoryIdQuery)) {
                $query->where('event.category_id', $categoryIdQuery);
            }

            if (!empty($eventNameQuery)) {
                $query->where('event.event_name', 'like', '%' . $eventNameQuery . '%');
            }

            if (!empty($event_status_Query)) {
                $query->where('event.event_status_id', $event_status_Query);
            }

            $events = $query->get();

            // ✅ Convert thumbnails to full URLs
         /*   $events->transform(function ($event) {
                $event->thumbnail = $event->thumbnail
                    ? asset('storage/' . $event->thumbnail)
                    : null;
                return $event;
            });*/

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /* public function getAllEvent(Request $request): JsonResponse
     {
         try {
             $categoryIdQuery = $request->query('category_id');
             $event_status_Query = $request->query('event_status_id');
             $eventNameQuery = $request->query('event_name');

             $query = Event::query()
                 ->join('event_status', 'event_status.id', '=', 'event.event_status_id')
                 ->select(
                     'event.event_id',
                     'event.event_name',
                     'event.thumbnail',
                     'event.event_descriptions',
                     'event.price',
                     'event.date',
                     'event.location',
                     'event.total_ticket',
                     'event.available_ticket',
                     'event.category_id',
                     'event_status.name AS status'
                 );

             if (!empty($categoryIdQuery)) {
                 $query->where('event.category_id', $categoryIdQuery);
             }

             if (!empty($eventNameQuery)) {
                 $query->where('event.event_name', 'like', '%' . $eventNameQuery . '%');
             }
             if(!empty($event_status_Query)){
                 $query->where('event.event_status_id', $event_status_Query);
             }

             $events = $query->get();

             return response()->json([
                 'status' => 200,
                 'message' => 'success',
                 'data' => $events,
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'error',
                 'message' => $e->getMessage(),
             ], 500);
         }
     }*/

    public function getEventById(int $eventId): JsonResponse
    {
        try {
            $event = Event::find($eventId);

            if (!$event) {
                return response()->json([
                    'message' => 'Event not found',
                    'status' => 404,
                ], 404);
            }

            // ✅ Convert thumbnail to full URL
            $event->thumbnail = $event->thumbnail
                ? asset('storage/' . $event->thumbnail)
                : null;

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $event,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /*  public function getEventById(int $eventId): JsonResponse
      {
          try {
              // Find the event by ID
  //            $getEventByIdQuery = Event::query()->where('event_id', $eventId)->first()
              $getEventByIdQuery = Event::query()->get()->find($eventId);


              // If the event doesn't exist, return a 404 error
              if (!$getEventByIdQuery) {
                  return response()->json([
                      'message' => 'Event not found',
                      'status' => 404,
                  ], 404);
              }

              // Return the event object directly in the response
              return response()->json([
                  'message' => 'success',
                  'status' => 200,
                  'data' => $getEventByIdQuery,
              ]);
          } catch (\Exception $e) {
              return response()->json(['error' => $e->getMessage()], 500);
          }
      }*/


    public function createEventByAdmin(Request $request): JsonResponse
    {
        try {
            $userAuth = Auth::guard('api')->user();
            if (!$userAuth || $userAuth->role_id !== 1) {
                return response()->json(['error' => 'Unauthorized access'], 401);
            }


            $validateBodyReq = Facades\Validator::make($request->all(), [
                'event_name' => 'required|string',
                'event_descriptions' => 'required|string',
                'date' => 'required|date',
                'thumbnail' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
                'total_ticket' => 'required|integer',
                'available_ticket' => 'required|integer',
                'price' => 'required|integer',
                'event_status_id' => 'required|integer',
                'category_id' => 'required|integer',
                'city' => 'required|string',

            ]);
            if ($validateBodyReq->fails()) {
                return response()->json(['error' => $validateBodyReq->errors()], 401);
            }
            /*  $thumbnailPath = null;
              if ($request->hasFile('thumbnail')) {
                  $file = $request->file('thumbnail');
                  $filename = time() . '_' . $file->getClientOriginalName();
                  $path = $file->storeAs('thumbnails', $filename, 'public');
                  $thumbnailPath = asset('storage/' . $path); // Generates full public URL
              }*/

            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('thumbnails', $filename, 'public');

                // ✅ Save only relative path like: 'thumbnails/xyz.jpg'
                $thumbnailPath = $path;
            }
            Event::query()->create([
                'event_name' => $request->event_name,
                'event_descriptions' => $request->event_descriptions,
                'date' => $request->date,
                'thumbnail' => $thumbnailPath,
                'total_ticket' => $request->total_ticket,
                'available_ticket' => $request->available_ticket,
                'event_status_id' => $request->event_status_id,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'location' => [
                    'city' => $request->city ?? 'Default City',
                    'latitude' => 12.345678,     // your fixed latitude
                    'longitude' => 98.765432     // your fixed longitude
                ],
            ]);
            return response()->json([
                'message' => 'Event created successfully',
                'status' => 201,
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateEventByAdmin(Request $request, int $eventId): JsonResponse
    {
        try {
            /*  $userAuth = Auth::guard('api')->user()->getAuthIdentifier()->role_id;
              if ($userAuth !== 1) {
                  return response()->json(['error' => 'Unauthorized access'], 401);
              }*/
            $userAuth = Auth::guard('api')->user();
            if (!$userAuth || $userAuth->role_id !== 1) {
                return response()->json(['error' => 'Unauthorized access'], 401);
            }
            $event = Event::query()->where('event_id', $eventId)->first();
            if (!$event) return response()->json([
                'error' => 'Event not Found '], 404);
            $validateBodyReq = $request->validate([
                'event_name' => 'sometimes|string',
                'event_description' => 'sometimes|string',
                'date' => 'sometimes|date',
                'total_ticket' => 'sometimes|integer',
                'available_ticket' => 'sometimes|integer',
                'event_status_id' => 'sometimes|integer',
                'category_id' => 'sometimes|integer',
                'price' => 'sometimes|numeric',
            ]);
            $event->update($validateBodyReq);
            return response()->json([
                'message' => 'Event created successfully',
                'data' => null,
                'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteEventByAdmin(int $eventId): JsonResponse
    {
        try {
//            $userAuth = Auth::guard('api')->user()->getAuthIdentifier()->role_id;
            /*$userAuth = Auth::guard('api')->user();
            if (!$userAuth || $userAuth->role_id !== 1) {
                return response()->json(['error' => 'Unauthorized access'], 401);
            }*/
            /*
                        if ($userAuth !== 1) {
                            return response()->json(['error' => 'Unauthorized access'], 401);
                        }*/
            $checkEvent_Id = Event::query()->where('event_id', $eventId)->first();
            if (!$checkEvent_Id) return response()->json(['error' => 'Event not Found '], 404);
            $checkEvent_Id->delete();
            return response()->json([
                'message' => 'Event deleted successfully',
                'status' => 200,
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
