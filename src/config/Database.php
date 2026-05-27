<?php

namespace App\Config;

class Database
{
    private static ?self $instance = null;
    private ?\PDO $connection = null;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? '72.60.42.81';
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '5432';
        $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'sap_errors';
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'ntp2026';
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? 'admin@123#';

        $dsn = "pgsql:host={$host};port={$port};dbname={$name}";

        $this->connection = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }
}
