<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/Database.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/*
|--------------------------------------------------------------------------
| ROUTER CONFIGURATION
|--------------------------------------------------------------------------
*/

$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    // Homepage
    $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'home']);

    // Authentication
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLogin']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);

    // Rooms (Student)
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'list']);

    // Reservations (Student)
    $r->addRoute('GET', '/reservations/create', ['App\Controllers\ReservationController', 'showForm']);
    $r->addRoute('POST', '/reservations/store', ['App\Controllers\ReservationController', 'store']);
    $r->addRoute('GET', '/reservations/success', ['App\Controllers\ReservationController', 'success']);
    $r->addRoute('GET', '/my-reservations', ['App\Controllers\ReservationController', 'index']);
    $r->addRoute('POST', '/reservations/cancel', ['App\Controllers\ReservationController', 'cancel']);
    $r->addRoute('GET', '/reservations/edit', ['App\Controllers\ReservationController', 'edit']);
    $r->addRoute('POST', '/reservations/update', ['App\Controllers\ReservationController', 'update']);

    // Admin Rooms
    $r->addRoute('GET', '/admin/rooms', ['App\Controllers\RoomController', 'adminIndex']);
    $r->addRoute('POST', '/admin/rooms/store', ['App\Controllers\RoomController', 'store']);
    $r->addRoute('POST', '/admin/rooms/delete', ['App\Controllers\RoomController', 'delete']);

    // Admin Reservations
    $r->addRoute('GET', '/admin/reservations', ['App\Controllers\AdminController', 'listReservations']);
    $r->addRoute('POST', '/admin/reservations/delete', ['App\Controllers\AdminController', 'deleteReservation']);

    // Time Slot Management
    $r->addRoute('POST', '/admin/slots/store', ['App\Controllers\TimeSlotController', 'store']);
    $r->addRoute('POST', '/admin/slots/update', ['App\Controllers\TimeSlotController', 'update']);
    $r->addRoute('POST', '/admin/slots/delete', ['App\Controllers\TimeSlotController', 'delete']);
    $r->addRoute('GET', '/admin/slots/by-room', ['App\Controllers\TimeSlotController', 'byRoom']);

    // API
    $r->addRoute('GET', '/api/available-slots', ['App\Controllers\ApiController', 'availableSlots']);
    $r->addRoute('GET', '/api/room-slots', ['App\Controllers\ApiController', 'roomSlots']);
});

/*
|--------------------------------------------------------------------------
| DISPATCH
|--------------------------------------------------------------------------
*/

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {

    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "404 Not Found";
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;

    case FastRoute\Dispatcher::FOUND:

        [$controllerClass, $method] = $routeInfo[1];

        $user = $_SESSION['user'] ?? null;
        $role = $user['role'] ?? null;

        // Admin Guard
        if (str_starts_with($uri, '/admin')) {
            if (!$user || $role !== 'admin') {
                header('Location: /login?error=unauthorized');
                exit;
            }
        }

        // Student Guard
        if (
            str_starts_with($uri, '/reservations') ||
            str_starts_with($uri, '/my-reservations')
        ) {
            if (!$user || $role !== 'student') {
                header('Location: /login?error=unauthorized');
                exit;
            }
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new RuntimeException("Controller method not found.");
        }

        $controller->$method();
        break;
}
