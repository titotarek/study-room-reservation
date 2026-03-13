<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;
use PDOException;
use RuntimeException;
use DateTime;
use App\Repositories\RoomRepository;

class ReservationRepository implements IReservationRepository
{
    private PDO $db;
    private RoomRepository $roomRepository;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->roomRepository = new RoomRepository();
    }

    /* =====================================================
       SLOT METHODS
    ====================================================== */

    public function getAvailableSlotsByRoomAndDate(
        int $roomId,
        string $date,
        ?int $reservationId = null
    ): array {

        try {

            $dateObj = DateTime::createFromFormat('Y-m-d', $date);

            if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
                return [];
            }

            $dayName = $dateObj->format('l');

            $stmt = $this->db->prepare("
                SELECT *
                FROM time_slots
                WHERE room_id = :room_id
                  AND day_of_week = :day
                ORDER BY start_time ASC
            ");

            $stmt->execute([
                'room_id' => $roomId,
                'day' => $dayName
            ]);

            $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$slots) {
                return [];
            }

            if ($reservationId !== null) {

                $stmt = $this->db->prepare("
                    SELECT time_slot_id
                    FROM reservations
                    WHERE room_id = :room_id
                      AND date = :date
                      AND id != :reservation_id
                ");

                $stmt->execute([
                    'room_id' => $roomId,
                    'date' => $date,
                    'reservation_id' => $reservationId
                ]);

            } else {

                $stmt = $this->db->prepare("
                    SELECT time_slot_id
                    FROM reservations
                    WHERE room_id = :room_id
                      AND date = :date
                ");

                $stmt->execute([
                    'room_id' => $roomId,
                    'date' => $date
                ]);
            }

            $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $available = [];

            foreach ($slots as $slot) {

                if (!in_array($slot['id'], $booked)) {
                    $available[] = $slot;
                }
            }

            return $available;

        } catch (\Throwable $e) {

            error_log("AVAILABLE SLOTS ERROR: " . $e->getMessage());
            return [];
        }
    }

public function getSlotsByRoom(int $roomId, string $reservationDate): array
{
    try {

        return $this->roomRepository
            ->getTimeSlotsByRoom($roomId, $reservationDate);

    } catch (PDOException $e) {

        error_log("ReservationRepository::getSlotsByRoom error: " . $e->getMessage());
        throw new RuntimeException("Failed to retrieve slots.");
    }
}

    public function getSlotsByRoomAndDate(
        int $roomId,
        string $date,
        int $excludeId = 0
    ): array {

        return $this->getAvailableSlotsByRoomAndDate(
            $roomId,
            $date,
            $excludeId ?: null
        );
    }

    public function isRoomAvailable(
        int $roomId,
        string $date,
        int $slotId,
        int $excludeId = 0
    ): bool {

        try {

            $stmt = $this->db->prepare("
                SELECT COUNT(*)
                FROM reservations
                WHERE room_id = :room_id
                  AND date = :date
                  AND time_slot_id = :slot_id
                  AND id != :exclude_id
            ");

            $stmt->execute([
                'room_id' => $roomId,
                'date' => $date,
                'slot_id' => $slotId,
                'exclude_id' => $excludeId
            ]);

            $count = (int)$stmt->fetchColumn();

            return $count === 0;

        } catch (PDOException $e) {

            error_log("Availability error: " . $e->getMessage());
            throw new RuntimeException("Failed to check availability.");
        }
    }

    /* =====================================================
       CREATE
    ====================================================== */

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO reservations 
            (user_id, room_id, time_slot_id, date, num_people)
            VALUES 
            (:user_id, :room_id, :time_slot_id, :date, :num_people)
        ");

        return $stmt->execute([
            'user_id' => $data['user_id'],
            'room_id' => $data['room_id'],
            'time_slot_id' => $data['time_slot_id'],
            'date' => $data['date'],
            'num_people' => $data['num_people'] ?? 1
        ]);
    }

    /* =====================================================
       FETCH
    ====================================================== */

    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, rm.room_number, rm.capacity, ts.start_time, ts.end_time
            FROM reservations r
            JOIN rooms rm ON r.room_id = rm.id
            JOIN time_slots ts ON r.time_slot_id = ts.id
            WHERE r.user_id = :user_id
            ORDER BY r.date ASC, ts.start_time ASC
        ");

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id, int $userId)
    {
        $stmt = $this->db->prepare("
            SELECT r.*, rm.room_number, rm.capacity, ts.start_time, ts.end_time
            FROM reservations r
            JOIN rooms rm ON r.room_id = rm.id
            JOIN time_slots ts ON r.time_slot_id = ts.id
            WHERE r.id = :id
              AND r.user_id = :user_id
        ");

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =====================================================
       DELETE
    ====================================================== */

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM reservations
            WHERE id = :id
              AND user_id = :user_id
        ");

        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);
    }

    /* =====================================================
       UPDATE
    ====================================================== */

    public function update(int $id, int $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE reservations
            SET room_id      = :room_id,
                time_slot_id = :time_slot_id,
                date         = :date,
                num_people   = :num_people
            WHERE id      = :id
              AND user_id = :user_id
        ");

        return $stmt->execute([
            'room_id' => $data['room_id'],
            'time_slot_id' => $data['time_slot_id'],
            'date' => $data['date'],
            'num_people' => $data['num_people'],
            'id' => $id,
            'user_id' => $userId
        ]);
    }

    /* =====================================================
       DETAILS
    ====================================================== */

    public function getReservationDetails(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   rm.room_number,
                   rm.building,
                   ts.start_time,
                   ts.end_time
            FROM reservations r
            JOIN rooms rm ON r.room_id = rm.id
            JOIN time_slots ts ON r.time_slot_id = ts.id
            WHERE r.id = :id
        ");

        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* =====================================================
       SLOT DELETE WITH CASCADE
    ====================================================== */

    public function getFutureReservationsBySlot(int $slotId): array
    {
        try {

            $stmt = $this->db->prepare("
                SELECT r.id, r.date, r.num_people,
                       u.name AS user_name,
                       u.email AS user_email,
                       rm.room_number
                FROM reservations r
                JOIN rooms rm ON r.room_id = rm.id
                JOIN users u ON r.user_id = u.id
                WHERE r.time_slot_id = :slot_id
                  AND r.date >= CURDATE()
                ORDER BY r.date ASC
            ");

            $stmt->execute(['slot_id' => $slotId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\Throwable $e) {

            error_log("getFutureReservationsBySlot error: " . $e->getMessage());
            return [];
        }
    }

    public function deleteSlotWithCascade(int $slotId): array
    {
        try {

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT COUNT(*)
                FROM reservations
                WHERE time_slot_id = :slot_id
                  AND date >= CURDATE()
            ");

            $stmt->execute(['slot_id' => $slotId]);

            $affected = (int)$stmt->fetchColumn();

            $this->db->prepare("
                DELETE FROM reservations
                WHERE time_slot_id = :slot_id
                  AND date >= CURDATE()
            ")->execute(['slot_id' => $slotId]);

            $this->db->prepare("
                DELETE FROM time_slots
                WHERE id = :slot_id
            ")->execute(['slot_id' => $slotId]);

            $this->db->commit();

            return [
                'success' => true,
                'deleted' => $affected
            ];

        } catch (\Throwable $e) {

            $this->db->rollBack();

            error_log("deleteSlotWithCascade error: " . $e->getMessage());

            return [
                'success' => false,
                'deleted' => 0
            ];
        }
    }

    public function getSlotsForRoom(int $roomId, string $date): array
{
    $stmt = $this->db->prepare("
        SELECT 
            ts.id,
            ts.room_id,
            ts.day_of_week,
            ts.start_time,
            ts.end_time,
            CASE 
                WHEN r.id IS NOT NULL THEN 1
                ELSE 0
            END AS is_taken
        FROM time_slots ts
        LEFT JOIN reservations r
            ON r.time_slot_id = ts.id
            AND r.date = :date
        WHERE ts.room_id = :room_id
        ORDER BY ts.start_time ASC
    ");

    $stmt->execute([
        'room_id' => $roomId,
        'date' => $date
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}