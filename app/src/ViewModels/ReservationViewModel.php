<?php

namespace App\ViewModels;

class ReservationViewModel
{
    public int $id;
    public int $roomId;
    public int $timeSlotId;
    public string $room;
    public string $date;           // Formatted for display: "Feb 11, 2026"
    public string $rawDate;        // Raw for forms: "2026-02-11"
    public string $time;
    public int $numPeople;

    /**
     * ViewModel constructor
     * Receives raw reservation data and prepares it for the view
     */
    public function __construct(array $reservation)
    {
        // Ensure ID is always an integer
        $this->id = (int)($reservation['id'] ?? $reservation['reservation_id'] ?? 0);

        // IDs for foreign keys
        $this->roomId = (int)($reservation['room_id'] ?? 0);
        $this->timeSlotId = (int)($reservation['time_slot_id'] ?? 0);

        // Room label
        $roomNumber = $reservation['room_number'] ?? null;
        $this->room = $roomNumber ? 'Room ' . $roomNumber : '—';

        // ✅ Date Handling: Added explicit fallbacks to empty strings to prevent null issues in views
        $rawDate = $reservation['date'] ?? $reservation['reservation_date'] ?? '';
        
        // If rawDate is empty, default to today for the form, otherwise keep the DB value
        $this->rawDate = !empty($rawDate) ? $rawDate : date('Y-m-d');  
        
        // Display format
        $this->date = !empty($rawDate) ? date('M d, Y', strtotime($rawDate)) : '—';

        // Time range formatting
        $start = !empty($reservation['start_time']) ? substr($reservation['start_time'], 0, 5) : '--:--';
        $end   = !empty($reservation['end_time']) ? substr($reservation['end_time'], 0, 5) : '--:--';
        $this->time = $start . ' - ' . $end;

        // Group size (default to 1)
        $this->numPeople = (int)($reservation['num_people'] ?? 1);
    }
}