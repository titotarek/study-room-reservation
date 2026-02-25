<?php

namespace App\Services;

use App\Config\Database;
use PDO;
use PDOException;
use RuntimeException;

class AdminReservationService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * ✅ Returns data formatted for AdminReservationViewModel
     */
    public function getAllReservations(): array
    {
        try {
            $query = "
                SELECT 
                    r.id,
                    u.name AS studentName,
                    u.email AS studentEmail,
                    rm.room_number AS room,
                    r.date AS date,
                    CONCAT(
                        SUBSTRING(ts.start_time,1,5),
                        ' - ',
                        SUBSTRING(ts.end_time,1,5)
                    ) AS time
                FROM reservations r
                JOIN users u ON r.user_id = u.id
                JOIN rooms rm ON r.room_id = rm.id
                JOIN time_slots ts ON r.time_slot_id = ts.id
                ORDER BY r.date DESC, ts.start_time ASC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("AdminReservationService::getAllReservations error: " . $e->getMessage());
            throw new RuntimeException("Failed to fetch reservations.");
        }
    }

    /**
     * ✅ Admin can delete ANY reservation
     * This automatically removes it from student panel
     */
    public function deleteReservation(int $id): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM reservations WHERE id = :id"
            );

            $stmt->execute(['id' => $id]);

        } catch (PDOException $e) {
            error_log("AdminReservationService::deleteReservation error: " . $e->getMessage());
            throw new RuntimeException("Failed to delete reservation.");
        }
    }
}
