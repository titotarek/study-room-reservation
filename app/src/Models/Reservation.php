<?php

namespace App\Models;

class Reservation {
    public int $id;
    public int $user_id;
    public int $room_id;
    public int $time_slot_id;
    public string $reservation_date;
}