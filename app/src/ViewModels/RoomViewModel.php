<?php

namespace App\ViewModels;

use App\Models\Room;

class RoomViewModel
{
    public int $id;
    public string $roomNumber;
    public int $capacity;
    public string $location;
    public string $equipment;
    public array $weeklySlots = []; 

    public function __construct(Room|array $room)
    {
        // Convert object to array if necessary
        $data = $room instanceof Room ? get_object_vars($room) : $room;

        // Ensure IDs are integers
        $this->id         = (int) ($data['id'] ?? 0);

        // Room number mapping with safety fallbacks
        $this->roomNumber = (string) ($data['room_number'] ?? $data['roomNumber'] ?? 'Unknown');
        
        // Capacity safety
        $this->capacity   = (int) ($data['capacity'] ?? 0);

        // Location mapping (handling building vs location naming)
        $this->location   = (string) ($data['location'] ?? $data['building'] ?? 'N/A');

        // Equipment description
        $this->equipment  = (string) ($data['equipment'] ?? 'None');
    }
}