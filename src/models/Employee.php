<?php

namespace App\Models;

use App\Config\Database;

class Employee
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findOrCreate(string $name): array
    {
        $name = trim($name);
        $stmt = $this->db->prepare("SELECT * FROM employees WHERE LOWER(name) = LOWER(?) LIMIT 1");
        $stmt->execute([$name]);
        $employee = $stmt->fetch();

        if ($employee) {
            return $employee;
        }

        $stmt = $this->db->prepare("INSERT INTO employees (name) VALUES (?) RETURNING *");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM employees ORDER BY name");
        return $stmt->fetchAll();
    }
}
