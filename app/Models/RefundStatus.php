<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundStatus extends Model
{
    protected $table = 'refund_status';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];
}
