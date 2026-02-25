<?php

namespace App\ViewModels;

use App\Models\TimeSlot;

class TimeSlotViewModel
{
    public int $id;
    public int $roomId;
    public string $day;
    public string $start;
    public string $end;
    public string $time;
    public bool $isBooked;

    public function __construct(TimeSlot|array $slot)
    {
        $data = $slot instanceof TimeSlot
            ? get_object_vars($slot)
            : $slot;

        $this->id       = (int) ($data['id'] ?? 0);
        $this->roomId   = (int) ($data['room_id'] ?? 0);
        $this->day      = (string) ($data['day_of_week'] ?? '');
        $this->start    = substr((string) ($data['start_time'] ?? ''), 0, 5);
        $this->end      = substr((string) ($data['end_time'] ?? ''), 0, 5);

        $this->time     = $this->start && $this->end
            ? "{$this->start} - {$this->end}"
            : '';

        $this->isBooked = (bool) ($data['is_booked'] ?? false);
    }
}
