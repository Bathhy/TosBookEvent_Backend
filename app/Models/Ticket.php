<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $primaryKey = 'ticket_id';
    protected $table = 'event_booking.ticket';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'booking_id',
        'ticket_status_id',
        'qr_code_text',
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class , 'booking_id', 'booking_id');
    }
}
