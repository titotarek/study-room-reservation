<?php

namespace App\Services;

use App\Repositories\ITimeSlotRepository;
use App\Repositories\TimeSlotRepository;
use App\Config\Database;
use PDO;
use RuntimeException;

class TimeSlotService
{
    private ITimeSlotRepository $repository;
    private PDO $db;

    public function __construct()
    {
        $this->repository = new TimeSlotRepository();
        $this->db = Database::getConnection();
    }

    /**
     * Add a new time slot with FULL business validation (HARDENED)
     */
    public function addSlot(array $data): bool
    {
        try {

            // ---- Required fields validation ----
            if (
                empty($data['room_id']) ||
                empty($data['day_of_week']) ||
                empty($data['start_time']) ||
                empty($data['end_time'])
            ) {
                return false;
            }

            $roomId    = (int)$data['room_id'];
            $day       = trim($data['day_of_week']);
            $startTime = $data['start_time'];
            $endTime   = $data['end_time'];

            // ---- Validate time format HH:MM or HH:MM:SS ----
            if (!$this->isValidTime($startTime) || !$this->isValidTime($endTime)) {
                return false;
            }

            // ---- Business rule: end > start ----
            if (strtotime($endTime) <= strtotime($startTime)) {
                return false;
            }

            // ---- Overlap protection (CRITICAL) ----
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM time_slots
                WHERE room_id = :room_id
                  AND day_of_week = :day
                  AND (
                        (:start_time < end_time AND :end_time > start_time)
                  )
            ");

            $stmt->execute([
                'room_id'   => $roomId,
                'day'       => $day,
                'start_time'=> $startTime,
                'end_time'  => $endTime
            ]);

            $conflicts = (int)$stmt->fetchColumn();

            if ($conflicts > 0) {
                return false;
            }

            // ---- Prevent exact duplicate ----
            $dupStmt = $this->db->prepare("
                SELECT COUNT(*) FROM time_slots
                WHERE room_id = :room_id
                  AND day_of_week = :day
                  AND start_time = :start_time
                  AND end_time = :end_time
            ");

            $dupStmt->execute([
                'room_id'   => $roomId,
                'day'       => $day,
                'start_time'=> $startTime,
                'end_time'  => $endTime
            ]);

            if ((int)$dupStmt->fetchColumn() > 0) {
                return false;
            }

            // ---- Safe to create ----
            return $this->repository->create($data);

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    /**
     * Get all weekly slots for a room
     */
    public function getSlotsForRoom(int $roomId): array
    {
        try {
            return $this->repository->getByRoom($roomId);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function removeSlot(int $id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    /* ==========================================
       PRIVATE HELPERS
    ========================================== */

    private function isValidTime(string $time): bool
    {
        return (bool) preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time);
    }
}
