<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventStatus extends Model
{
    protected $table = 'event_status';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];

    public function event(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Event::class, 'event_status_id', 'id');
    }
}
