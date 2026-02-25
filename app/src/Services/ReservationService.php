<?php

namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Config\Database;
use PDO;
use PDOException;
use RuntimeException;

class ReservationService
{
    private ReservationRepository $repo;
    private PDO $db;

    public function __construct()
    {
        $this->repo = new ReservationRepository();
        $this->db   = Database::getConnection();
    }

    /* =====================================================
       SLOT METHODS
    ====================================================== */

    public function getSlotsForRoom(int $roomId): array
    {
        try {
            return $this->repo->getSlotsByRoom($roomId);
        } catch (\Throwable $e) {
            error_log("getSlotsForRoom error: " . $e->getMessage());
            return [];
        }
    }

    public function getSlotById(?int $id): ?array
    {
        if (!$id) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT *
                FROM time_slots
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            $slot = $stmt->fetch(PDO::FETCH_ASSOC);

            return $slot ?: null;

        } catch (PDOException $e) {
            error_log("getSlotById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * API METHOD - CLEANED
     */
    public function getAvailableSlots(
        int $roomId,
        string $date,
        ?int $reservationId = null
    ): array {

        if (!$roomId || !$this->isValidDate($date)) {
            return [];
        }

        try {
            return $this->repo->getAvailableSlotsByRoomAndDate(
                $roomId,
                $date,
                $reservationId
            );

        } catch (\Throwable $e) {
            error_log("getAvailableSlots error: " . $e->getMessage());
            return [];
        }
    }

    /* =====================================================
       CREATE RESERVATION
    ====================================================== */

    public function createReservation(array $data): int|bool
    {
        try {

            if (
                empty($data['user_id']) ||
                empty($data['room_id']) ||
                empty($data['time_slot_id'])
            ) {
                return false;
            }

            $date = $data['reservation_date'] ?? null;

            if (!$date || !$this->isValidDate($date)) {
                return false;
            }

            if ($date < date('Y-m-d')) {
                return false;
            }

            $slot = $this->getSlotById((int)$data['time_slot_id']);
            if (!$slot || $slot['start_time'] >= $slot['end_time']) {
                return false;
            }

            $roomStmt = $this->db->prepare("
                SELECT capacity 
                FROM rooms 
                WHERE id = :id 
                LIMIT 1
            ");
            $roomStmt->execute(['id' => $data['room_id']]);
            $room = $roomStmt->fetch(PDO::FETCH_ASSOC);

            $numPeople = (int)($data['num_people'] ?? 1);

            if (!$room || $numPeople <= 0 || $numPeople > (int)$room['capacity']) {
                return false;
            }

            if (
                !$this->repo->isRoomAvailable(
                    (int)$data['room_id'],
                    $date,
                    (int)$data['time_slot_id']
                )
            ) {
                return false;
            }

            $data['reservation_date'] = $date;

            $success = $this->repo->create($data);

            if (!$success) {
                return false;
            }

            return (int)$this->db->lastInsertId();

        } catch (\Throwable $e) {
            error_log("createReservation error: " . $e->getMessage());
            return false;
        }
    }

    /* =====================================================
       UPDATE RESERVATION
    ====================================================== */

    public function updateReservation(int $id, int $userId, array $data): bool
    {
        try {

            if (!$id || !$userId) {
                return false;
            }

            $date = $data['reservation_date'] ?? null;

            if (
                empty($data['room_id']) ||
                empty($data['time_slot_id']) ||
                !$date ||
                !$this->isValidDate($date)
            ) {
                return false;
            }

            if ($date < date('Y-m-d')) {
                return false;
            }

            $slot = $this->getSlotById((int)$data['time_slot_id']);
            if (!$slot || $slot['start_time'] >= $slot['end_time']) {
                return false;
            }

            if (
                !$this->repo->isRoomAvailable(
                    (int)$data['room_id'],
                    $date,
                    (int)$data['time_slot_id'],
                    $id
                )
            ) {
                return false;
            }

            $data['reservation_date'] = $date;

            return $this->repo->update($id, $userId, $data);

        } catch (\Throwable $e) {
            error_log("updateReservation error: " . $e->getMessage());
            return false;
        }
    }

    /* =====================================================
       FETCH USER DATA
    ====================================================== */

    public function getUserReservations(int $userId): array
    {
        try {
            return $this->repo->getByUserId($userId);
        } catch (\Throwable $e) {
            error_log("getUserReservations error: " . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id, int $userId): ?array
    {
        try {
            return $this->repo->find($id, $userId);
        } catch (\Throwable $e) {
            error_log("getById error: " . $e->getMessage());
            return null;
        }
    }

    /* =====================================================
       SUCCESS PAGE DATA
    ====================================================== */

    public function getDetailedReservation(int $id, int $userId): ?array
    {
        try {

            $stmt = $this->db->prepare("
                SELECT 
                    r.id,
                    r.date,
                    r.num_people,
                    rm.room_number,
                    ts.start_time,
                    ts.end_time
                FROM reservations r
                JOIN rooms rm ON r.room_id = rm.id
                JOIN time_slots ts ON r.time_slot_id = ts.id
                WHERE r.id = :id
                  AND r.user_id = :user_id
                LIMIT 1
            ");

            $stmt->execute([
                'id'      => $id,
                'user_id' => $userId
            ]);

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$res) {
                return null;
            }

            $res['display_time'] =
                substr($res['start_time'], 0, 5) .
                ' - ' .
                substr($res['end_time'], 0, 5);

            return $res;

        } catch (\Throwable $e) {
            error_log("getDetailedReservation error: " . $e->getMessage());
            return null;
        }
    }

    /* =====================================================
       CANCEL
    ====================================================== */

    public function cancelReservation(int $id, int $userId): bool
    {
        try {
            return $this->repo->delete($id, $userId);
        } catch (\Throwable $e) {
            error_log("cancelReservation error: " . $e->getMessage());
            return false;
        }
    }

    /* =====================================================
       HELPER
    ====================================================== */

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /* =====================================================
       RESERVATION DETAILS (used by success page)
    ====================================================== */

    public function getReservationDetails(int $id): ?array
    {
        try {
            return $this->repo->getReservationDetails($id);
        } catch (\Throwable $e) {
            error_log("getReservationDetails error: " . $e->getMessage());
            return null;
        }
    }
}