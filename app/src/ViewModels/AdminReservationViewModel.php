<?php

namespace App\ViewModels;

class AdminReservationViewModel
{
    public int $id;
    public string $studentName;
    public string $studentEmail;
    public string $room;
    public string $date;
    public string $time;
    public int $numPeople;

    public function __construct(array $reservation)
    {
        $this->id = (int)($reservation['id'] ?? 0);

        $this->studentName  = (string)($reservation['studentName'] ?? '—');
        $this->studentEmail = (string)($reservation['studentEmail'] ?? '—');

        $this->room = (string)($reservation['room'] ?? '—');

        // Keep raw DB date (your view already formats if needed)
        $this->date = (string)($reservation['date'] ?? '—');

        $this->time = (string)($reservation['time'] ?? '--:-- - --:--');

        $this->numPeople = (int)($reservation['num_people'] ?? 1);
    }
}
