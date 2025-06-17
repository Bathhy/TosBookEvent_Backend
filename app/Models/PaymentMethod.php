<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'payment_method';
    protected $primaryKey = 'payment_method_id';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'image'
    ];
    protected $appends = ['image_url'];
    protected $hidden = ['image'];
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
    public function paymentMethod():HasMany
    {
        return $this->hasMany(Payment::class);
    }

}
