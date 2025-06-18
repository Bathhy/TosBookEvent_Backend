<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table = 'event';
    protected $primaryKey = 'event_id';
    public $timestamps = false;
    protected $fillable = [
        'event_name',
        'event_descriptions',
        'thumbnail',
        'date',
        'location',
        'total_ticket',
        'available_ticket',
        'event_status_id',
        'category_id',
        'price'
    ];

    public function eventStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EventStatus::class, 'event_status_id', );
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function getImageUrlAttribute()
    {
        if ($this->thumbnail) {
//            return url('storage/' . $this->thumbnail);
            return secure_url('storage/' . $this->thumbnail);
            // or if using asset()
            // return asset('storage/' . $this->image);
        }
        return null;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    protected $appends = ['image_url'];

    protected $casts = [
        'location' => 'array',
        'price' => 'double',
    ];
}
