<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'report';
    protected $primaryKey = 'report_id';
    public $timestamps = false;
    protected $fillable = [
        'date_from',
        'date_to',
        'total_income',
        'total_booking',
        'total_refunds',
        'net_income',
        'report_create',
    ];
}
