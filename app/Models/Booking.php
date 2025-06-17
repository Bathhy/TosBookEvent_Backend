<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $table = 'booking';
    protected $primaryKey = 'booking_id';
    public $timestamps = false;
    protected $fillable = [
        'event_id',
        'customer_id',
        'quantity',
        'total_amount',
        'booking_status_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id')->with('eventStatus');
    }
    public function ticket()
    {
        return $this->hasMany(Ticket::class, 'booking_id', 'booking_id');
    }
    public function bookingStatus()
    {
        return $this->belongsTo(BookingStatus::class, 'booking_status_id');
    }
}
