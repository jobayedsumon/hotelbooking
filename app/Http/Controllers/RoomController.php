<?php

namespace App\Http\Controllers;

use App\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    //
    public function index()
    {
        $rooms = Room::all();

        if ($rooms) {
            return response()->json($rooms, 200);
        } else {
            return response()->json('No rooms found.', 404);
        }


    }
}
