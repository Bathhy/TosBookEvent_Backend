<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'booking_id' => $this->booking_id,
            'quantity' => $this->quantity,
            'total_amount' => $this->total_amount,
            'event' => new EventResource($this->whenLoaded('event')),
            'bookingStatus' => $this->whenLoaded('bookingStatus'),
            // Add other booking fields as needed
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
