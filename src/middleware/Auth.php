<?php

namespace App\middleware;

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::start();
        return isset($_SESSION['employee_id']) && isset($_SESSION['employee_name']);
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: sap-error-log/public/login.php');
            exit;
        }
    }

    public static function getEmployee(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id' => $_SESSION['employee_id'],
            'name' => $_SESSION['employee_name'],
        ];
    }

    public static function login(int $id, string $name): void
    {
        self::start();
        $_SESSION['employee_id'] = $id;
        $_SESSION['employee_name'] = $name;
    }

    public static function logout(): void
    {
        self::start();
        session_destroy();
    }
}
