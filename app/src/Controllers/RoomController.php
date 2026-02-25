<?php

namespace App\Controllers;

use App\Services\RoomService;
use App\Services\ReservationService;
use App\ViewModels\RoomViewModel;
use RuntimeException;

class RoomController
{
    private RoomService $service;
    private ReservationService $reservationService;

    public function __construct()
    {
        $this->service = new RoomService();
        $this->reservationService = new ReservationService();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * STUDENT VIEW: Display the Room Gallery
     * URL: /rooms
     */
    public function list(): void
    {
        try {
            $roomsRaw = $this->service->getAllRooms();

            $rooms = array_map(
                fn(array $room) => new RoomViewModel($room),
                $roomsRaw
            );

            // ✅ ADD WEEKLY SLOTS TO EACH ROOM
            foreach ($rooms as $room) {
                $room->weeklySlots = $this->reservationService
                    ->getSlotsForRoom($room->id);
            }

            require __DIR__ . '/../Views/rooms_list.php';

        } catch (RuntimeException $e) {
            error_log("RoomController::list error: " . $e->getMessage());
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load rooms.</p>";
        }
    }

    /**
     * ADMIN VIEW: Manage Rooms & Slots
     * URL: /admin/rooms
     */
    public function adminIndex(): void
    {
        try {
            $this->checkAdmin();

            $roomsRaw = $this->service->getAllRooms();

            $rooms = array_map(
                fn(array $room) => new RoomViewModel($room),
                $roomsRaw
            );

            require __DIR__ . '/../Views/admin/rooms.php';

        } catch (RuntimeException $e) {
            error_log("RoomController::adminIndex error: " . $e->getMessage());
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load admin rooms.</p>";
        }
    }

    /**
     * ADMIN: Create new room
     */
    public function store(): void
    {
        try {
            $this->checkAdmin();

            $roomNumber = trim($_POST['room_number'] ?? '');
            $capacity   = (int) ($_POST['capacity'] ?? 0);
            $building   = trim($_POST['location'] ?? '');

            if ($roomNumber === '' || $capacity <= 0 || $building === '') {
                header('Location: /admin/rooms?error=invalid_input');
                exit;
            }

            $data = [
                'room_number' => $roomNumber,
                'building'    => $building,
                'capacity'    => $capacity,
                'equipment'   => $_POST['equipment'] ?? null
            ];

            if (
                !empty($_POST['day_of_week']) &&
                !empty($_POST['start_time']) &&
                !empty($_POST['end_time'])
            ) {
                $data['day_of_week'] = $_POST['day_of_week'];
                $data['start_time']  = $_POST['start_time'];
                $data['end_time']    = $_POST['end_time'];
            }

            $roomId = $this->service->createRoom($data);

            if ($roomId) {
                header('Location: /admin/rooms?status=created');
            } else {
                header('Location: /admin/rooms?error=create_failed');
            }

            exit;

        } catch (RuntimeException $e) {
            error_log("RoomController::store error: " . $e->getMessage());
            header('Location: /admin/rooms?error=server_error');
            exit;
        }
    }

    /**
     * ADMIN: Delete room
     */
    public function delete(): void
    {
        try {
            $this->checkAdmin();

            $id = $_GET['id'] ?? null;

            if ($id && $this->service->deleteRoom((int) $id)) {
                header('Location: /admin/rooms?status=deleted');
            } else {
                header('Location: /admin/rooms?error=delete_failed');
            }

            exit;

        } catch (RuntimeException $e) {
            error_log("RoomController::delete error: " . $e->getMessage());
            header('Location: /admin/rooms?error=server_error');
            exit;
        }
    }

    /**
     * ADMIN GUARD
     */
    private function checkAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;
        $role = $user['role'] ?? null;

        if (!$user || $role !== 'admin') {
            header('Location: /rooms?error=unauthorized');
            exit;
        }
    }
}
