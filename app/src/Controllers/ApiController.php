<?php

namespace App\Controllers;

use App\Services\ReservationService;

class ApiController
{
    private ReservationService $service;

    public function __construct()
    {
        $this->service = new ReservationService();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function availableSlots()
    {
        header('Content-Type: application/json');

        try {

            $roomId = (int)($_GET['room_id'] ?? 0);
            $date   = $_GET['date'] ?? null;
            $reservationId = isset($_GET['reservation_id']) && $_GET['reservation_id'] !== ''
                ? (int)$_GET['reservation_id']
                : null;

            if (!$roomId || !$date) {
                echo json_encode([
                    'success' => false,
                    'error'   => 'Missing parameters',
                    'slots'   => []
                ]);
                return;
            }

            $slots = $this->service->getAvailableSlots(
                $roomId,
                $date,
                $reservationId
            );

            echo json_encode([
                'success' => true,
                'slots'   => $slots
            ]);

        } catch (\Throwable $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'error'   => $e->getMessage(),
                'slots'   => []
            ]);
        }
    }
}
