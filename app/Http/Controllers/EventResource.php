<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'event_id' => $this->event_id,
            'event_name' => $this->event_name,
            'event_descriptions' => $this->event_descriptions,
            'thumbnail' => $this->getThumbnailUrl(),
            'date' => $this->date,
            'location' => $this->location,
            'total_ticket' => $this->total_ticket,
            'available_ticket' => $this->available_ticket,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'eventStatus' => $this->whenLoaded('eventStatus'),
            'category' => $this->whenLoaded('category'),
        ];
    }

    private function getThumbnailUrl()
    {
        if (!$this->thumbnail) {
            return null;
        }

        // If it's already a full URL, return as is
        if (filter_var($this->thumbnail, FILTER_VALIDATE_URL)) {
            return $this->thumbnail;
        }

        // Convert relative path to full URL
        return asset('storage/' . $this->thumbnail);

        // Alternative using Laravel Storage:
        // return Storage::disk('public')->url('thumbnails/' . $this->thumbnail);
    }
}
