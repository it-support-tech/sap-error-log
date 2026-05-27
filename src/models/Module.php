<?php

namespace App\Models;

use App\Config\Database;

class Module
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT m.*, 
                   COUNT(e.id) as total_errors,
                   SUM(CASE WHEN e.status = 'resolved' THEN 1 ELSE 0 END) as resolved_count,
                   SUM(CASE WHEN e.status = 'pending' THEN 1 ELSE 0 END) as pending_count
            FROM modules m
            LEFT JOIN error_logs e ON m.id = e.module_id
            GROUP BY m.id
            ORDER BY m.name_en
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM modules WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM modules WHERE code = ?");
        $stmt->execute([$code]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
