<?php

namespace App\Controllers;

use App\Services\AdminReservationService;
use App\ViewModels\AdminReservationViewModel;
use App\Repositories\TimeSlotRepository;
use RuntimeException;

class AdminController
{
    private AdminReservationService $service;

    public function __construct()
    {
        $this->service = new AdminReservationService();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * ✅ LIST ALL RESERVATIONS (ADMIN)
     */
    public function listReservations()
    {
        try {

            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                header('Location: /login?error=unauthorized');
                exit;
            }

            $reservationsRaw = $this->service->getAllReservations();

            $reservations = array_map(
                fn(array $res) => new AdminReservationViewModel($res),
                $reservationsRaw
            );

            require __DIR__ . '/../Views/admin/reservation_list.php';

        } catch (RuntimeException $e) {

            error_log("AdminController::listReservations error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load reservations. Please try again later.</p>";
        }
    }

    /**
     * ✅ ADMIN DELETE ANY RESERVATION
     */
    public function deleteReservation()
    {
        try {

            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                header('Location: /login?error=unauthorized');
                exit;
            }

            $id = $_POST['id'] ?? null;

            if (!$id) {
                header("Location: /admin/reservations?status=invalid");
                exit;
            }

            $this->service->deleteReservation((int)$id);

            header("Location: /admin/reservations?status=deleted");
            exit;

        } catch (RuntimeException $e) {

            error_log("AdminController::deleteReservation error: " . $e->getMessage());

            header("Location: /admin/reservations?status=error");
            exit;
        }
    }

    /**
     * ✅ RETURN TIME SLOTS BY ROOM (FOR SLOT MODAL)
     */
    public function byRoom()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([]);
            return;
        }

        $roomId = $_GET['room_id'] ?? null;

        if (!$roomId) {
            echo json_encode([]);
            return;
        }

        $repo = new TimeSlotRepository();
        $slots = $repo->getByRoom((int)$roomId);

        echo json_encode($slots);
    }
}