<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->index()->foreign('room_number')->references('room_number')->on('rooms');
            $table->dateTime('arrival');
            $table->dateTime('checkout');
            $table->foreignId('customer_id')->unique()->references('id')->on('users')->onDelete('cascade');
            $table->string('book_type');
            $table->dateTime('book_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
