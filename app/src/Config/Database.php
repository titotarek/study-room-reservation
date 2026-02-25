<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {

            $host = 'mysql';
            $db   = 'developmentdb';
            $user = 'root';
            $pass = 'secret123';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

            try {
                self::$connection = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {

                // Log real error internally
                error_log(sprintf(
                    "[%s] Database connection error: %s\n",
                    date('Y-m-d H:i:s'),
                    $e->getMessage()
                ));

                // Throw generic runtime exception (handled globally in index.php)
                throw new RuntimeException(
                    'Unable to connect to the database. Please try again later.'
                );
            }
        }

        return self::$connection;
    }
}
