<?php

namespace App\Services;

use App\Repositories\UserRepository;
use RuntimeException;

class AuthService
{
    private UserRepository $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Attempt to log in a user
     */
    public function attempt(string $email, string $password): bool
    {
        try {
            error_log('AUTH: Login attempt started');

            // Normalize email
            $email = trim($email);
            error_log("AUTH: Email entered: {$email}");

            $user = $this->repo->findByEmail($email);

            if (!$user) {
                error_log('AUTH: FAILURE - User not found');
                return false;
            }

            error_log('AUTH: User found. Verifying password...');
            error_log('AUTH: Password length BEFORE cleanup: ' . strlen($password));
            error_log('AUTH: Hash length: ' . strlen($user['password']));

            // Remove invisible newline characters from form input
            $password = rtrim($password);

            error_log('AUTH: Password length AFTER cleanup: ' . strlen($password));

            if (!password_verify($password, $user['password'])) {
                error_log('AUTH: FAILURE - password_verify failed');
                return false;
            }

            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role']
            ];

            error_log('AUTH: SUCCESS - Login successful for ' . $email);

            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    /**
     * Log out the user
     */
    public function logout(): void
    {
        try {
            error_log('AUTH: User logged out');

            session_unset();
            session_destroy();

        } catch (\Throwable $e) {
            error_log('AUTH: Logout error - ' . $e->getMessage());
            throw new RuntimeException("Failed to log out user.");
        }
    }

    /**
     * Check login status
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Get logged-in user role
     */
    public function getRole(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }
}
