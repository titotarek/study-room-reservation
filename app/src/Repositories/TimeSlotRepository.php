<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;
use PDOException;
use RuntimeException;

class TimeSlotRepository implements ITimeSlotRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new time slot
     */
    public function create(array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO time_slots (room_id, day_of_week, start_time, end_time)
                 VALUES (:room_id, :day_of_week, :start_time, :end_time)"
            );

            return $stmt->execute([
                'room_id'     => (int) $data['room_id'],
                'day_of_week' => $data['day_of_week'],
                'start_time'  => $data['start_time'],
                'end_time'    => $data['end_time']
            ]);

        } catch (PDOException $e) {
            error_log("TimeSlotRepository::create error: " . $e->getMessage());
            throw new RuntimeException("Failed to create time slot.");
        }
    }

    /**
     * Get all weekly slots for a room
     */
    public function getByRoom(int $roomId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT *
                 FROM time_slots
                 WHERE room_id = :room_id
                 ORDER BY FIELD(
                    day_of_week,
                    'Monday','Tuesday','Wednesday',
                    'Thursday','Friday','Saturday','Sunday'
                 ),
                 start_time ASC"
            );

            $stmt->execute(['room_id' => $roomId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("TimeSlotRepository::getByRoom error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve time slots.");
        }
    }

    public function getAvailableSlotsByRoomAndDate(
        int $roomId,
        string $date,
        ?int $reservationId = null
    ): array {

        try {
            $dayName = date('l', strtotime($date));

            $sql = "
                SELECT 
                    ts.id,
                    ts.start_time,
                    ts.end_time,
                    CONCAT(
                        SUBSTRING(ts.start_time,1,5),
                        ' - ',
                        SUBSTRING(ts.end_time,1,5)
                    ) AS display_time
                FROM time_slots ts
                WHERE ts.room_id = :room_id_main
                  AND ts.day_of_week = :day_name
                  AND ts.id NOT IN (
                        SELECT time_slot_id
                        FROM reservations
                        WHERE room_id = :room_id_sub
                          AND date = :selected_date
                          " . ($reservationId !== null ? "AND id != :res_id" : "") . "
                  )
                ORDER BY ts.start_time ASC
            ";

            $stmt = $this->db->prepare($sql);

            $params = [
                'room_id_main'  => $roomId,
                'room_id_sub'   => $roomId,
                'day_name'      => $dayName,
                'selected_date' => $date,
            ];

            if ($reservationId !== null) {
                $params['res_id'] = $reservationId;
            }

            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("TimeSlotRepository::getAvailableSlotsByRoomAndDate error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve available time slots.");
        }
    }

    /**
     * Delete a time slot by ID — cancels future reservations first
     * to satisfy the foreign key constraint.
     */
    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Remove future reservations linked to this slot first
            $this->db->prepare("
                DELETE FROM reservations
                WHERE time_slot_id = :slot_id
                  AND date >= CURDATE()
            ")->execute(['slot_id' => $id]);

            // Now delete the slot itself
            $stmt = $this->db->prepare(
                "DELETE FROM time_slots WHERE id = :id"
            );
            $result = $stmt->execute(['id' => $id]);

            $this->db->commit();

            return $result;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("TimeSlotRepository::delete error: " . $e->getMessage());
            throw new RuntimeException("Failed to delete time slot.");
        }
    }

    /**
     * Find a single time slot by ID
     */
    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM time_slots WHERE id = :id LIMIT 1"
            );

            $stmt->execute(['id' => $id]);

            $slot = $stmt->fetch(PDO::FETCH_ASSOC);

            return $slot ?: null;

        } catch (PDOException $e) {
            error_log("TimeSlotRepository::find error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve time slot.");
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE time_slots
                 SET day_of_week = :day_of_week,
                     start_time  = :start_time,
                     end_time    = :end_time
                 WHERE id = :id"
            );

            return $stmt->execute([
                'day_of_week' => $data['day_of_week'],
                'start_time'  => $data['start_time'],
                'end_time'    => $data['end_time'],
                'id'          => $id
            ]);

        } catch (PDOException $e) {
            error_log("TimeSlotRepository::update error: " . $e->getMessage());
            throw new RuntimeException("Failed to update time slot.");
        }
    }
}