<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/Database.php';

session_start();

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/*
|--------------------------------------------------------------------------
| GLOBAL ERROR & EXCEPTION HANDLING
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '0');
error_reporting(E_ALL);

set_error_handler(function (
    int $severity,
    string $message,
    string $file,
    int $line
) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (Throwable $exception) {

    http_response_code(500);

    $isApiRequest = str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api');

    error_log(sprintf(
        "[%s] %s in %s on line %d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));

    if ($isApiRequest) {
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'error',
            'message' => 'An unexpected server error occurred.'
        ]);
        return;
    }

    echo "<h1>500 Internal Server Error</h1>";
    echo "<p>Something went wrong. Please try again later.</p>";
});

/*
|--------------------------------------------------------------------------
| ROUTER CONFIGURATION
|--------------------------------------------------------------------------
*/

$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    // --- Homepage ---
    $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'home']);

    // --- Auth ---
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLogin']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'showRegister']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);

    // --- Rooms (Student View) ---
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'list']);

    // --- Reservations (Student CRUD) ---
    $r->addRoute('GET', '/reservations/create', ['App\Controllers\ReservationController', 'showForm']);
    $r->addRoute('POST', '/reservations/store', ['App\Controllers\ReservationController', 'store']);
    $r->addRoute('GET', '/reservations/success', ['App\Controllers\ReservationController', 'success']);
    // $r->addRoute('GET', '/reservations/success', ['App\Controllers\ReservationController', 'success']);
    $r->addRoute('GET', '/my-reservations', ['App\Controllers\ReservationController', 'index']);
    $r->addRoute('GET', '/reservations/cancel', ['App\Controllers\ReservationController', 'cancel']);
    $r->addRoute('GET', '/reservations/edit', ['App\Controllers\ReservationController', 'edit']);
    $r->addRoute('POST', '/reservations/update', ['App\Controllers\ReservationController', 'update']);

    // --- Admin Rooms ---
    $r->addRoute('GET', '/admin/rooms', ['App\Controllers\RoomController', 'adminIndex']);
    $r->addRoute('GET', '/admin/rooms/create', ['App\Controllers\RoomController', 'create']);
    $r->addRoute('POST', '/admin/rooms/store', ['App\Controllers\RoomController', 'store']);
    $r->addRoute('GET', '/admin/rooms/edit', ['App\Controllers\RoomController', 'edit']);
    $r->addRoute('POST', '/admin/rooms/update', ['App\Controllers\RoomController', 'update']);
    $r->addRoute('GET', '/admin/rooms/delete', ['App\Controllers\RoomController', 'delete']);

    // --- Admin Reservations ---
    $r->addRoute('GET', '/admin/reservations', ['App\Controllers\AdminController', 'listReservations']);
    $r->addRoute('POST', '/admin/reservations/delete', ['App\Controllers\AdminController', 'deleteReservation']);

    // --- Time Slot Management ---
    $r->addRoute('POST', '/admin/slots/store', ['App\Controllers\TimeSlotController', 'store']);
    $r->addRoute('POST', '/admin/slots/update', ['App\Controllers\TimeSlotController', 'update']); // ✅ ADDED
    $r->addRoute('GET', '/admin/slots/delete', ['App\Controllers\TimeSlotController', 'delete']);
    $r->addRoute('GET', '/admin/slots/by-room', ['App\Controllers\TimeSlotController', 'byRoom']);

    // --- CLEAN JSON API ---
    $r->addRoute('GET', '/api/available-slots', ['App\Controllers\ApiController', 'availableSlots']);
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
        echo "404 Not Found - The route $uri does not exist.";
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;

    case FastRoute\Dispatcher::FOUND:

        [$controllerClass, $method] = $routeInfo[1];

        $user = $_SESSION['user'] ?? null;
        $role = $user['role'] ?? null;

        // --- Admin Guard ---
        if (str_starts_with($uri, '/admin')) {
            if (!$user || $role !== 'admin') {
                header('Location: /login?error=unauthorized');
                exit;
            }
        }

        // --- Student Guard ---
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