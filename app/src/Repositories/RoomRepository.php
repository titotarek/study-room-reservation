<?php

namespace App\Repositories;

use App\Config\Database;
use App\Repositories\IRoomRepository;
use PDO;
use PDOException;
use RuntimeException;

class RoomRepository implements IRoomRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* =========================
       ROOMS
    ==========================*/

    public function getAllRooms(): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM rooms ORDER BY room_number ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("RoomRepository::getAllRooms error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve rooms.");
        }
    }

    public function find(int $id): ?array
    {
        try {

            $stmt = $this->db->prepare(
                "SELECT * FROM rooms WHERE id = :id LIMIT 1"
            );

            $stmt->execute(['id' => $id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: null;

        } catch (PDOException $e) {

            error_log("RoomRepository::find error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve room.");
        }
    }

    public function create(array $data): int|bool
    {
        try {

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO rooms (room_number, building, capacity, equipment)
                VALUES (:room_number, :building, :capacity, :equipment)
            ");

            $stmt->execute([
                'room_number' => $data['room_number'],
                'building' => $data['building'],
                'capacity' => (int)$data['capacity'],
                'equipment' => $data['equipment'] ?? null
            ]);

            $roomId = (int)$this->db->lastInsertId();

            if (
                isset($data['day_of_week'], $data['start_time'], $data['end_time'])
                && $data['day_of_week']
                && $data['start_time']
                && $data['end_time']
            ) {

                $slotStmt = $this->db->prepare("
                    INSERT INTO time_slots (room_id, day_of_week, start_time, end_time)
                    VALUES (:room_id, :day_of_week, :start_time, :end_time)
                ");

                $slotStmt->execute([
                    'room_id' => $roomId,
                    'day_of_week' => $data['day_of_week'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time']
                ]);
            }

            $this->db->commit();

            return $roomId;

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log("Room creation failed: " . $e->getMessage());
            throw new RuntimeException("Failed to create room.");
        }
    }

    public function update(int $id, array $data): bool
    {
        try {

            $stmt = $this->db->prepare("
                UPDATE rooms
                SET room_number = :room_number,
                    building = :building,
                    capacity = :capacity,
                    equipment = :equipment
                WHERE id = :id
            ");

            return $stmt->execute([
                'room_number' => $data['room_number'],
                'building' => $data['building'],
                'capacity' => (int)$data['capacity'],
                'equipment' => $data['equipment'] ?? null,
                'id' => $id
            ]);

        } catch (PDOException $e) {

            error_log("RoomRepository::update error: " . $e->getMessage());
            throw new RuntimeException("Failed to update room.");
        }
    }

    public function delete(int $id): bool
    {
        try {

            $this->db->beginTransaction();

            $this->db->prepare(
                "DELETE FROM reservations WHERE room_id = :id"
            )->execute(['id' => $id]);

            $this->db->prepare(
                "DELETE FROM time_slots WHERE room_id = :id"
            )->execute(['id' => $id]);

            $result = $this->db->prepare(
                "DELETE FROM rooms WHERE id = :id"
            )->execute(['id' => $id]);

            $this->db->commit();

            return $result;

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log("RoomRepository::delete error: " . $e->getMessage());
            throw new RuntimeException("Failed to delete room.");
        }
    }

    /* =========================
       TIME SLOTS
    ==========================*/

    public function getTimeSlotsByRoom(int $roomId, ?string $reservationDate = null): array
{
    try {

        $stmt = $this->db->prepare("
            SELECT
                ts.id,
                ts.room_id,
                ts.day_of_week,
                ts.start_time,
                ts.end_time,
                r.date AS reservation_date

            FROM time_slots ts

            LEFT JOIN reservations r
                ON r.time_slot_id = ts.id
                AND r.room_id = ts.room_id

            WHERE ts.room_id = :room_id

            ORDER BY
                FIELD(
                    ts.day_of_week,
                    'Monday','Tuesday','Wednesday',
                    'Thursday','Friday','Saturday','Sunday'
                ),
                ts.start_time ASC
        ");

        $stmt->execute([
            'room_id' => $roomId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        error_log("RoomRepository::getTimeSlotsByRoom error: " . $e->getMessage());
        throw new RuntimeException("Failed to retrieve time slots.");
    }
}

    public function deleteTimeSlot(int $slotId): bool
    {
        try {

            $stmt = $this->db->prepare(
                "DELETE FROM time_slots WHERE id = :id"
            );

            return $stmt->execute(['id' => $slotId]);

        } catch (PDOException $e) {

            error_log("RoomRepository::deleteTimeSlot error: " . $e->getMessage());
            throw new RuntimeException("Failed to delete time slot.");
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

                WHERE ts.room_id = :room_id
                AND ts.day_of_week = :day_name

                AND NOT EXISTS (
                    SELECT 1
                    FROM reservations r
                    WHERE r.time_slot_id = ts.id
                    AND r.date = :selected_date
                    AND (:res_id IS NULL OR r.id != :res_id)
                )

                ORDER BY ts.start_time ASC
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                'room_id' => $roomId,
                'selected_date' => $date,
                'day_name' => $dayName,
                'res_id' => $reservationId
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            error_log("RoomRepository::getAvailableSlotsByRoomAndDate error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve available time slots.");
        }
    }
}