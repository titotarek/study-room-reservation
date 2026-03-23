<?php

namespace App\Controllers;

use App\Services\RoomService;
use RuntimeException;

class HomeController
{
    private RoomService $roomService;

    public function __construct()
    {
        $this->roomService = new RoomService();
    }

    public function home()
    {
        try {
            $rooms = $this->roomService->getAllRooms();

            require_once __DIR__ . '/../Views/home.php';

        } catch (RuntimeException $e) {

            error_log("HomeController::home error: " . $e->getMessage());

            http_response_code(500);

            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load homepage. Please try again later.</p>";
        }
    }
}
