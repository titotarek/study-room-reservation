<?php

namespace App\Controllers;

use App\Services\AuthService;
use RuntimeException;

class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* =====================================================
       CSRF HELPERS
    ===================================================== */

    private function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function validateCsrfToken(): bool
    {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token']) &&
               hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    /* =====================================================
       SHOW LOGIN
    ===================================================== */

    public function showLogin()
    {
        try {
            if ($this->auth->isLoggedIn()) {
                $this->redirectBasedOnRole();
            }

            $csrfToken = $this->generateCsrfToken();

            require __DIR__ . '/../Views/login.php';

        } catch (RuntimeException $e) {
            error_log("AuthController::showLogin error: " . $e->getMessage());
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Unable to load login page.</p>";
        }
    }

    /* =====================================================
       LOGIN
    ===================================================== */

    public function login()
    {
        try {

            // 🔐 CSRF CHECK
            if (!$this->validateCsrfToken()) {
                http_response_code(403);
                exit('Invalid CSRF token.');
            }

            // 1. Clean input
            $email = isset($_POST['email'])
                ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL))
                : '';

            $password = isset($_POST['password'])
                ? trim($_POST['password'])
                : '';

            if (empty($email) || empty($password)) {
                header("Location: /login?error=missing_fields");
                exit;
            }

            if ($this->auth->attempt($email, $password)) {
                $this->redirectBasedOnRole();
            }

            header("Location: /login?error=1");
            exit;

        } catch (RuntimeException $e) {

            error_log("AuthController::login error: " . $e->getMessage());

            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Authentication failed due to a server error.</p>";
        }
    }

    /* =====================================================
       REDIRECT
    ===================================================== */

    private function redirectBasedOnRole()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
            header("Location: /admin/rooms");
        } else {
            header("Location: /rooms");
        }
        exit;
    }

    /* =====================================================
       LOGOUT
    ===================================================== */

    public function logout()
    {
        try {
            $this->auth->logout();
            header("Location: /login");
            exit;

        } catch (RuntimeException $e) {
            error_log("AuthController::logout error: " . $e->getMessage());
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Logout failed.</p>";
        }
    }
}
