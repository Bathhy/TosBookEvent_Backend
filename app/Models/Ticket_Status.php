<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket_Status extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'ticket_status';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
}
