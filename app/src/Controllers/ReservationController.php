<?php

namespace App\Controllers;

use App\Security\Csrf;
use App\Services\ReservationService;
use App\Services\RoomService;
use App\ViewModels\ReservationViewModel;
use RuntimeException;

class ReservationController
{
    private ReservationService $service;
    private RoomService $rooms;

    public function __construct()
    {
        date_default_timezone_set('Europe/Amsterdam');

        $this->service = new ReservationService();
        $this->rooms   = new RoomService();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        try {

            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            $userId = (int) $_SESSION['user']['id'];

            $reservationsRaw = $this->service->getUserReservations($userId);

            $reservations = array_map(
                fn(array $reservation) => new ReservationViewModel($reservation),
                $reservationsRaw
            );
            $csrfToken = Csrf::token();

            require __DIR__ . '/../Views/my_reservations.php';

        } catch (RuntimeException $e) {

            error_log("ReservationController::index error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load reservations.</p>";
        }
    }

    public function showForm()
    {
        try {

            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            $roomId = $_GET['room_id'] ?? 1;
            $slotId = $_GET['slot_id'] ?? null;

            $rooms        = $this->rooms->getAllRooms();
            $slots        = $this->service->getSlotsForRoom((int)$roomId);
            $selectedRoom = $this->rooms->getRoomById((int)$roomId);
            $selectedSlot = $slotId ? $this->service->getSlotById((int)$slotId) : null;
            $csrfToken    = Csrf::token();

            require __DIR__ . '/../Views/reservation_form.php';

        } catch (RuntimeException $e) {

            error_log("ReservationController::showForm error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load reservation form.</p>";
        }
    }

   public function edit()
{
    try {

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $id     = $_GET['id'] ?? null;
        $userId = $_SESSION['user']['id'];

        $reservation = $this->service->getById((int)$id, $userId);

        if (!$reservation) {
            header('Location: /my-reservations?error=not_found');
            exit;
        }

        $rooms = $this->rooms->getAllRooms();
        $csrfToken = Csrf::token();

        // ✅ Load slots for THIS reservation date
        $slots = $this->service->getAvailableSlots(
            (int)$reservation['room_id'],
            $reservation['date'],
            (int)$reservation['id'] // allow current reservation slot
        );

        require __DIR__ . '/../Views/reservation_form.php';

    } catch (RuntimeException $e) {

        error_log("ReservationController::edit error: " . $e->getMessage());

        http_response_code(500);
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>Unable to load reservation.</p>";
    }
}

    /* ===============================
       STORE
    =============================== */

    public function store()
    {
        try {

            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
                http_response_code(403);
                exit('Invalid CSRF token.');
            }

            $slotId = $_POST['time_slot_id'] ?? $_POST['slot_id'] ?? null;
            $slot   = $slotId ? $this->service->getSlotById((int)$slotId) : null;

            if (!$slot || $slot['start_time'] >= $slot['end_time']) {
                header('Location: /rooms?error=invalid_time_slot');
                exit;
            }

            $reservationDate = $_POST['date'] ?? '';
            $today = date('Y-m-d');

            if (!$reservationDate || $reservationDate < $today) {
                header('Location: /rooms?error=past_date');
                exit;
            }

            $room = $this->rooms->getRoomById((int)$_POST['room_id']);
            $numPeople = (int)($_POST['num_people'] ?? 1);

            if (!$room || $numPeople <= 0 || $numPeople > (int)$room['capacity']) {
                header('Location: /rooms?error=invalid_group_size');
                exit;
            }

            $data = [
                'user_id'      => $_SESSION['user']['id'],
                'room_id'      => $_POST['room_id'],
                'time_slot_id' => $slotId,
                'date'         => $reservationDate,
                'num_people'   => $numPeople
            ];

            $newReservationId = $this->service->createReservation($data);

            if ($newReservationId) {

                header('Location: /reservations/success?id=' . $newReservationId);

            } else {

                header('Location: /rooms?error=collision');
            }

            exit;

        } catch (RuntimeException $e) {

            error_log("ReservationController::store error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to create reservation.</p>";
        }
    }

    /* ===============================
       UPDATE
    =============================== */

    public function update()
    {
    //      var_dump($_POST);
    // exit;
        try {

            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
                http_response_code(403);
                exit('Invalid CSRF token.');
            }

            $id     = $_POST['id'] ?? null;
            $userId = $_SESSION['user']['id'];

            $slotId = $_POST['time_slot_id'] ?? $_POST['slot_id'] ?? null;
            $slot   = $slotId ? $this->service->getSlotById((int)$slotId) : null;

            if (!$slot || $slot['start_time'] >= $slot['end_time']) {

                header("Location: /reservations/edit?id=$id&error=invalid_time_slot");
                exit;
            }

            /* FIXED — no past-date validation when editing */

            $reservationDate = $_POST['date'] ?? '';

            if (!$reservationDate) {

                header("Location: /reservations/edit?id=$id&error=invalid_date");
                exit;
            }

            $room = $this->rooms->getRoomById((int)$_POST['room_id']);
            $numPeople = (int)($_POST['num_people'] ?? 1);

            if (!$room || $numPeople <= 0 || $numPeople > (int)$room['capacity']) {

                header("Location: /reservations/edit?id=$id&error=invalid_group_size");
                exit;
            }

            $data = [
                'room_id'      => $_POST['room_id'],
                'time_slot_id' => $slotId,
                'date'         => $reservationDate,
                'num_people'   => $numPeople
            ];

            if ($id && $this->service->updateReservation((int)$id, $userId, $data)) {

                header('Location: /my-reservations?status=updated');

            } else {

                header("Location: /reservations/edit?id=$id&error=collision");
            }

            exit;

        } catch (RuntimeException $e) {

            error_log("ReservationController::update error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to update reservation.</p>";
        }
    }

    public function cancel()
    {
        try {

            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
                http_response_code(403);
                exit('Invalid CSRF token.');
            }

            $id     = $_POST['id'] ?? null;
            $userId = $_SESSION['user']['id'];

            if ($id && $this->service->cancelReservation((int)$id, $userId)) {

                header('Location: /my-reservations?status=cancelled');

            } else {

                header('Location: /my-reservations?error=failed');
            }

            exit;

        } catch (RuntimeException $e) {

            error_log("ReservationController::cancel error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to cancel reservation.</p>";
        }
    }

    public function success()
    {
        try {

            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            $id = $_GET['id'] ?? null;

            if (!$id) {
                header('Location: /my-reservations');
                exit;
            }

            $reservation = $this->service->getReservationDetails((int)$id);

            if (!$reservation) {

                header('Location: /my-reservations?error=not_found');
                exit;
            }

            $reservationVM = new ReservationViewModel($reservation);

            require __DIR__ . '/../Views/reservation_success.php';

        } catch (RuntimeException $e) {

            error_log("ReservationController::success error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load success page.</p>";
        }
    }
}
