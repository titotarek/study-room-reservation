<?php

namespace App\Models;

class TimeSlot {
    public int $id;
    public int $room_id;
    public string $start_time;
    public string $end_time;
}