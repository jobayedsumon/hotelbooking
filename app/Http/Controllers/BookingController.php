<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Payment;
use App\Room;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class BookingController extends Controller
{
    //

    public function rules(): array
    {
        return [
            'room_number' => 'required|string|exists:rooms',
            'arrival' => 'required|date|',
            'checkout' => 'required|date',
            'amount' => 'required|numeric',
            'book_type' => 'required|string',
            'customer_id' => 'unique:bookings',
        ];
    }

    public function index()
    {

        $bookings = Booking::all();
        $booking_list = array();

        foreach ($bookings as $booking) {

            $data['customer_name'] = $booking->customer->first_name . ' ' . $booking->customer->last_name;
            $data['booked_room'] = $booking->room->room_number;
            $data['arrival'] = $booking->arrival;
            $data['checkout'] = $booking->checkout;
            $data['total_paid'] = $booking->payment->amount;

            array_push($booking_list, $data);
        }

        return response()->json($booking_list, 200);
    }


    public function store(Request $request)
    {
        $request['customer_id'] = auth('api')->id();

        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $room = Room::where('room_number', $request->input('room_number'))->first();

        if ($bookedRoom = $room->booking) {
            if ($bookedRoom->checkout >= $request->input('arrival')) {
                return response()->json('The room is already booked', 206);
            }
        }

        try {
            $booking = Booking::create([
                'room_number' => $request->input('room_number'),
                'arrival' => $request->input('arrival'),
                'checkout' => $request->input('checkout'),
                'book_type' => $request->input('book_type'),
                'book_time' => now(),
                'customer_id' => auth('api')->id()
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'customer_id' => auth('api')->id(),
                'amount' => $request->input('amount'),
                'date' => now()
            ]);

            return response()->json('Booking completed successfully', 201);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }
    }

    public function destroy(Request $request)
    {
        $customer = auth('api')->user();

        if ($booking = $customer->booking) {

            if ($request->input('amount')) {

                $request->validate(['amount'=>'numeric']);
                $booking->payment->amount += $request->input('amount');
                $booking->payment->save();

                if ($amount = $booking->payment->amount < $price = $booking->room->price) {
                    return response()->json('Pay due amount of '.
                        $booking->room->price - $booking->payment->amount.
                        ' before checkout', 206);
                }
                else {
                    $booking->delete();
                    return response()->json('Checked out successfully', 200);
                }

            } else {

                if ($booking->payment->amount < $booking->room->price) {

                    return response()->json('Pay due amount of '.
                        $booking->room->price - $booking->payment->amount.
                        ' before checkout', 206);
                }
                else {
//                    $booking->delete();
                    return response()->json('Checked out successfully', 200);
                }
            }

        } else {
            return response()->json('No booking found', 404);
        }
    }
}
