<?php

namespace App\Services;

use App\Repositories\RoomRepository;
use App\Repositories\TimeSlotRepository;
use RuntimeException;

class RoomService
{
    private RoomRepository $roomRepository;
    private TimeSlotRepository $timeSlotRepository;

    public function __construct()
    {
        $this->roomRepository = new RoomRepository();
        $this->timeSlotRepository = new TimeSlotRepository();
    }

    // ---------------------------
    // ROOM METHODS
    // ---------------------------

    public function getAllRooms(): array
    {
        try {
            return $this->roomRepository->getAllRooms();
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function getRoomById(int $id): ?array
    {
        try {
            return $this->roomRepository->find($id);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

 
    public function createRoom(array $data): int|bool
    {
        try {
            // 1️⃣ Create room
            $roomId = $this->roomRepository->create($data);

            if (!$roomId) {
                return false;
            }

            // 2️⃣ If time slot data exists → create first slot
            if (
                !empty($data['day_of_week']) &&
                !empty($data['start_time']) &&
                !empty($data['end_time'])
            ) {
                $this->timeSlotRepository->create([
                    'room_id'     => $roomId,
                    'day_of_week' => $data['day_of_week'],
                    'start_time'  => $data['start_time'],
                    'end_time'    => $data['end_time']
                ]);
            }

            // 3️⃣ Return room ID
            return $roomId;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteRoom(int $id): bool
    {
        try {
            return $this->roomRepository->delete($id);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    // ---------------------------
    // TIME SLOT METHODS
    // ---------------------------

    public function getWeeklySlots(int $roomId): array
    {
        try {
            return $this->timeSlotRepository->getByRoom($roomId);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function getAvailableSlots(
        int $roomId,
        string $date,
        ?int $reservationId = null
    ): array {
        try {
            return $this->timeSlotRepository
                ->getAvailableSlotsByRoomAndDate($roomId, $date, $reservationId);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function createTimeSlot(array $data): bool
    {
        try {
            return $this->timeSlotRepository->create($data);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteTimeSlot(int $id): bool
    {
        try {
            return $this->timeSlotRepository->delete($id);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function updateTimeSlot(int $id, array $data): bool
{
    $repo = new \App\Repositories\TimeSlotRepository();
    return $repo->update($id, $data);
}
}
