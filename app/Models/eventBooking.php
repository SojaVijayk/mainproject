<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class eventBooking extends Model
{
    use HasFactory;
    protected $table='event_bookings';
    protected $fillable = ['id','from_date','to_date','booked_by','booked_on'];
}