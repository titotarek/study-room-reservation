<?php

namespace App\Repositories;

use App\Config\Database; 
use PDO;
use PDOException;
use RuntimeException;

class UserRepository implements IUserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("UserRepository::findByEmail error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve user by email.");
        }
    }

    public function verifyUser(string $email, string $password)
    {
        try {
            $user = $this->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            
            return false;

        } catch (PDOException $e) {
            error_log("UserRepository::verifyUser error: " . $e->getMessage());
            throw new RuntimeException("Failed to verify user.");
        }
    }

    public function findById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("UserRepository::findById error: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve user by ID.");
        }
    }
}
