<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function room()
    {
        return $this->hasOne(Room::class, 'room_number', 'room_number');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id');
    }
}
