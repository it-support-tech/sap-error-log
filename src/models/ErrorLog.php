<?php

namespace App\Models;

use App\Config\Database;

class ErrorLog
{
    private \PDO $db;
    private const PER_PAGE = 10;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByModule(int $moduleId, int $page = 1, string $search = ''): array
    {
        $offset = ($page - 1) * self::PER_PAGE;
        $params = [$moduleId];
        $searchSql = '';

        if ($search !== '') {
            $searchSql = " AND (e.error_message ILIKE ? OR e.symptom ILIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $stmt = $this->db->prepare("
            SELECT e.*, emp.name as employee_name, m.name_lo as module_name_lo, m.name_en as module_name_en
            FROM error_logs e
            JOIN employees emp ON e.employee_id = emp.id
            JOIN modules m ON e.module_id = m.id
            WHERE e.module_id = ? {$searchSql}
            ORDER BY e.occurred_at DESC, e.created_at DESC
            LIMIT " . self::PER_PAGE . " OFFSET {$offset}
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countByModule(int $moduleId, string $search = ''): int
    {
        $params = [$moduleId];
        $searchSql = '';

        if ($search !== '') {
            $searchSql = " AND (error_message ILIKE ? OR symptom ILIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM error_logs WHERE module_id = ? {$searchSql}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function search(string $query, int $page = 1): array
    {
        $offset = ($page - 1) * self::PER_PAGE;
        $like = "%{$query}%";

        $stmt = $this->db->prepare("
            SELECT e.*, emp.name as employee_name, m.name_lo as module_name_lo, m.name_en as module_name_en, m.color as module_color
            FROM error_logs e
            JOIN employees emp ON e.employee_id = emp.id
            JOIN modules m ON e.module_id = m.id
            WHERE e.error_message ILIKE ? OR e.symptom ILIKE ? OR e.cause ILIKE ? OR e.solution ILIKE ?
            ORDER BY e.occurred_at DESC
            LIMIT " . self::PER_PAGE . " OFFSET {$offset}
        ");
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function countSearch(string $query): int
    {
        $like = "%{$query}%";
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM error_logs
            WHERE error_message ILIKE ? OR symptom ILIKE ? OR cause ILIKE ? OR solution ILIKE ?
        ");
        $stmt->execute([$like, $like, $like, $like]);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, emp.name as employee_name, m.name_lo as module_name_lo, m.name_en as module_name_en, m.code as module_code, m.color as module_color, m.icon as module_icon
            FROM error_logs e
            JOIN employees emp ON e.employee_id = emp.id
            JOIN modules m ON e.module_id = m.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare("
            INSERT INTO error_logs (module_id, employee_id, occurred_at, error_message, symptom, cause, solution, video_url, image_path, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            RETURNING *
        ");
        $stmt->execute([
            $data['module_id'],
            $data['employee_id'],
            $data['occurred_at'],
            $data['error_message'],
            $data['symptom'],
            $data['cause'] ?? null,
            $data['solution'] ?? null,
            $data['video_url'] ?? null,
            $data['image_path'] ?? null,
            $data['status'] ?? 'pending',
        ]);
        return $stmt->fetch();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE error_logs SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getRecentAll(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, emp.name as employee_name, m.name_lo as module_name_lo, m.color as module_color
            FROM error_logs e
            JOIN employees emp ON e.employee_id = emp.id
            JOIN modules m ON e.module_id = m.id
            ORDER BY e.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getPerPage(): int
    {
        return self::PER_PAGE;
    }
   public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE error_logs SET 
                module_id = ?,
                occurred_at = ?,
                error_message = ?,
                symptom = ?,
                cause = ?,
                solution = ?,
                video_url = ?,
                image_path = ?,
                status = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['module_id'],
            $data['occurred_at'],
            $data['error_message'],
            $data['symptom'],
            $data['cause'] ?? null,
            $data['solution'] ?? null,
            $data['video_url'] ?? null,
            $data['image_path'] ?? null,
            $data['status'],
            $id
        ]);
    }
}
