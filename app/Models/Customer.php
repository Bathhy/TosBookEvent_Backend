<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model implements Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, \Illuminate\Auth\Authenticatable;

//    protected $connection = 'pgsql';
    //table
    protected $connection = 'pgsql';
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;
    protected $hidden = [
        'password',
    ];
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'profile_image',
        'role_id'
    ];


}
