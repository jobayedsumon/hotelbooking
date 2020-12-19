<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //
    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'room_number', 'room_number');
    }
}
