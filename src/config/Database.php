<?php

namespace App\Config;

class Database
{
    private static ?self $instance = null;
    private ?\PDO $connection = null;

    private function __construct()
    {
        $host = '72.60.42.81';         
        $port = '5432';            
        $name = 'sap_errors';
        $user = 'ntp2026';
        $pass = 'admin@123#';

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            $this->connection = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
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