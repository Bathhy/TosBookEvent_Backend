<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'refund';
    protected $primaryKey = 'refund_id';
    public $timestamps = false;
    protected $fillable = [
        'booking_id',
        'payment_id',
        'user_id',
        'refund_date',
        'refund_status',
        'refund_amount',
        'reason',
        'refund_status_id',
    ];
    protected $casts = [
        'refund_amount'=> 'double',
    ];
    public function refundStatus()
    {
        return $this->belongsTo(RefundStatus::class, 'refund_status_id', 'id');
    }

    public function bookings()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function payments()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id', 'customer_id');
    }

    public function status()
    {
        return $this->belongsTo(RefundStatus::class, 'status_id', 'id');
    }
}
