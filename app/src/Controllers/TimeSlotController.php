<?php

namespace App\Controllers;

use App\Services\RoomService;
use App\Repositories\ReservationRepository;
use RuntimeException;

class TimeSlotController
{
    private RoomService $service;

    public function __construct()
    {
        $this->service = new RoomService();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API: Weekly Slots
    |--------------------------------------------------------------------------
    */

    public function apiSlotsByRoom(): void
    {
        header('Content-Type: application/json');

        try {
            $roomId = (int) ($_GET['room_id'] ?? 0);

            if ($roomId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'slots' => []]);
                return;
            }

            $slots = $this->service->getWeeklySlots($roomId);

            echo json_encode(['success' => true, 'slots' => $slots ?? []]);

        } catch (RuntimeException $e) {
            error_log('TimeSlotController::apiSlotsByRoom error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'slots' => []]);
        }
    }

    public function apiAvailableSlots(): void
    {
        header('Content-Type: application/json');

        try {
            $roomId        = (int) ($_GET['room_id'] ?? 0);
            $date          = trim($_GET['date'] ?? '');
            $reservationId = isset($_GET['reservation_id']) && $_GET['reservation_id'] !== ''
                ? (int) $_GET['reservation_id']
                : null;

            if ($roomId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'slots' => []]);
                return;
            }

            $slots = $this->service->getAvailableSlots($roomId, $date, $reservationId);

            echo json_encode(['success' => true, 'slots' => $slots ?? []]);

        } catch (RuntimeException $e) {
            error_log('TimeSlotController::apiAvailableSlots error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'slots' => []]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: Create Slot
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        $this->checkAdmin();

        $roomId    = (int) ($_POST['room_id'] ?? 0);
        $day       = trim($_POST['day_of_week'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime   = $_POST['end_time'] ?? '';

        if ($roomId <= 0 || $day === '' || $startTime === '' || $endTime === '') {
            header('Location: /admin/rooms?error=invalid_input');
            exit;
        }

        // NEW VALIDATION: End time must be after start time
        if (strtotime($endTime) <= strtotime($startTime)) {
            header('Location: /admin/rooms?error=invalid_time');
            exit;
        }

        $data = [
            'room_id'     => $roomId,
            'day_of_week' => $day,
            'start_time'  => $startTime,
            'end_time'    => $endTime
        ];

        if ($this->service->createTimeSlot($data)) {
            header('Location: /admin/rooms?status=slot_added');
        } else {
            header('Location: /admin/rooms?error=save_failed');
        }

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: Update Slot
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        $this->checkAdmin();

        $slotId = (int) ($_POST['slot_id'] ?? 0);
        $roomId = (int) ($_POST['room_id'] ?? 0);
        $day    = trim($_POST['day_of_week'] ?? '');
        $start  = $_POST['start_time'] ?? '';
        $end    = $_POST['end_time'] ?? '';

        if ($slotId <= 0 || $roomId <= 0 || $day === '' || $start === '' || $end === '') {
            header('Location: /admin/rooms?error=invalid_input');
            exit;
        }

        // NEW VALIDATION: End time must be after start time
        if (strtotime($end) <= strtotime($start)) {
            header('Location: /admin/rooms?error=invalid_time');
            exit;
        }

        $data = [
            'day_of_week' => $day,
            'start_time'  => $start,
            'end_time'    => $end
        ];

        if ($this->service->updateTimeSlot($slotId, $data)) {
            header('Location: /admin/rooms?status=slot_updated');
        } else {
            header('Location: /admin/rooms?error=update_failed');
        }

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: Delete Slot — returns JSON so the modal UI updates instantly
    |--------------------------------------------------------------------------
    */

    public function delete(): void
    {
        header('Content-Type: application/json');

        $this->checkAdminJson();

        $slotId = (int) ($_GET['id'] ?? 0);

        if ($slotId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing slot ID']);
            return;
        }

        try {
            $repo   = new ReservationRepository();
            $result = $repo->deleteSlotWithCascade($slotId);

            if (!$result['success']) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to delete slot']);
                return;
            }

            echo json_encode([
                'success'                => true,
                'reservations_cancelled' => $result['deleted'],
                'message'                => $result['deleted'] > 0
                    ? "{$result['deleted']} future reservation(s) were cancelled."
                    : "Slot deleted successfully."
            ]);

        } catch (\Throwable $e) {
            error_log('TimeSlotController::delete error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: Fetch Slots For Modal
    |--------------------------------------------------------------------------
    */

    public function byRoom(): void
    {
        header('Content-Type: application/json');

        $roomId = (int) ($_GET['room_id'] ?? 0);

        if ($roomId <= 0) {
            echo json_encode([]);
            return;
        }

        $repo  = new \App\Repositories\TimeSlotRepository();
        $slots = $repo->getByRoom($roomId);

        echo json_encode($slots);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN GUARD — redirects (for form submissions)
    |--------------------------------------------------------------------------
    */

    private function checkAdmin(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'admin') {
            header('Location: /login?error=unauthorized');
            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN GUARD — returns JSON (for fetch/AJAX calls)
    |--------------------------------------------------------------------------
    */

    private function checkAdminJson(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }
}