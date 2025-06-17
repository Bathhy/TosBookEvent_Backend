<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingStatus extends Model
{
    protected $table = 'booking_status';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];
}
